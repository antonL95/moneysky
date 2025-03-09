<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Data\App\Services\TaggedTransactionData;
use App\Enums\CacheKeys;
use App\Exceptions\AiExceptions;
use App\Models\Scopes\UserScope;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankTransactionRaw;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use App\Models\UserTransaction;
use App\Models\UserTransactionTag;
use App\Services\AiService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

use function in_array;

final class ProcessTransactionsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const array STREAMING_SERVICES_IDENTIFIERS = [
        'Netflix',
        'Hulu',
        'Disney',
        'Amazon',
        'HBO',
        'AppleTV',
        'Peacock',
        'Spotify',
        'AppleMusic',
        'YouTube',
        'Tidal',
        'Paramount',
    ];

    public int $timeout = 900;

    /** @var int[] */
    private static array $userIds = [];

    private AiService $openAiService;

    private Logger $logger;

    /**
     * @param  Collection<int, UserBankTransactionRaw>  $rawTransactionsToProcess
     */
    public function __construct(
        public Collection $rawTransactionsToProcess,
    ) {}

    public function handle(AiService $openAiService, Logger $logger): void
    {
        $this->openAiService = $openAiService;
        $this->logger = $logger;

        $this->processTransactions();

        if (self::$userIds === []) {
            return;
        }

        foreach (self::$userIds as $userId) {
            try {
                $user = User::findOrFail($userId);
            } catch (ModelNotFoundException) {
                continue;
            }

            $user->budgets()->withoutGlobalScope(UserScope::class)
                ->each(function (UserBudget $userBudget): void {
                    $userBudget->periods()->where('start_date', '>=', Carbon::now()->startOfMonth())
                        ->where('end_date', '<=', Carbon::now()->endOfMonth())->each(
                            function (UserBudgetPeriod $userBudgetPeriod): void {
                                CalculateBudgetJob::dispatch($userBudgetPeriod);
                            },
                        );
                });
        }

        self::$userIds = [];
    }

    private function processTransactions(): void
    {
        foreach ($this->rawTransactionsToProcess as $transaction) {
            $userBankAccount = UserBankAccount::withoutGlobalScope(UserScope::class)->find($transaction->user_bank_account_id);

            if ($userBankAccount === null) {
                continue;
            }

            if (! in_array($userBankAccount->user_id, self::$userIds, true)) {
                self::$userIds[] = $userBankAccount->user_id;
            }

            $shouldTag = $this->processTransaction($transaction, $userBankAccount);

            if ($shouldTag) {
                try {
                    $taggedTransaction = $this->openAiService->classifyTransactions($transaction);
                    $this->tagTransaction($taggedTransaction, $transaction, $userBankAccount);
                } catch (AiExceptions $e) {
                    $this->logger->info($e->getMessage(), [
                        'transaction_id' => $transaction->id,
                    ]);

                    $this->saveUserTransaction($transaction, $userBankAccount);
                }
            }

            $transaction->processed = true;
            $transaction->save();
        }
    }

    private function processTransaction(
        UserBankTransactionRaw $transaction,
        UserBankAccount $userBankAccount,
    ): bool {
        // Find transactions between accounts
        $otherTransaction = UserBankTransactionRaw::where('external_id', '!=', $transaction->external_id)
            ->where('balance_cents', '=', -$transaction->balance_cents)
            ->where('currency', '=', $transaction->currency)
            ->where('booked_at', '=', $transaction->booked_at?->format('Y-m-d'))
            ->first();

        if ($otherTransaction !== null) {
            return false;
        }

        // Find transactions between accounts with the same external id
        $otherTransaction = UserBankTransactionRaw::where('external_id', $transaction->external_id)
            ->where('balance_cents', -$transaction->balance_cents)
            ->where('currency', $transaction->currency)
            ->where('booked_at', $transaction->booked_at?->format('Y-m-d'))
            ->first();

        if ($otherTransaction !== null) {
            return false;
        }

        if ($transaction->remittance_information === null && $transaction->additional_information === null) {
            return false;
        }

        // find streaming services
        if ($transaction->remittance_information !== null) {
            foreach (self::STREAMING_SERVICES_IDENTIFIERS as $service) {
                if (str_contains($transaction->remittance_information, $service)) {
                    $tag = TransactionTag::where('tag', '=', 'Streaming Services')->first();

                    if ($tag === null) {
                        $tag = TransactionTag::create([
                            'tag' => 'Streaming Services',
                            'color' => '#DC143C',
                        ]);
                    }

                    $this->saveUserTransaction($transaction, $userBankAccount, $tag);

                    return false;
                }
            }
        }

        if ($transaction->additional_information !== null) {
            foreach (self::STREAMING_SERVICES_IDENTIFIERS as $service) {
                if (str_contains($transaction->additional_information, $service)) {
                    $tag = TransactionTag::where('tag', '=', 'Streaming Services')->first();

                    if ($tag === null) {
                        $tag = TransactionTag::create([
                            'tag' => 'Streaming Services',
                            'color' => '#DC143C',
                        ]);
                    }

                    $this->saveUserTransaction($transaction, $userBankAccount, $tag);

                    return false;
                }
            }
        }

        // Find similar already tagged transactions
        $tenPercent = $transaction->balance_cents * 0.05;
        $similarTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
            ->where('user_id', '=', $userBankAccount->user_id)
            ->whereBetween('balance_cents', [$transaction->balance_cents - $tenPercent, $transaction->balance_cents + $tenPercent])
            ->where('currency', '=', $transaction->currency)
            ->where('description', 'like', '%'.$transaction->remittance_information.'%')
            ->first();

        if ($similarTransaction !== null) {
            $this->saveUserTransaction(
                $transaction,
                $userBankAccount,
                $similarTransaction->transactionTag,
                $similarTransaction->userTransactionTag,
            );

            return false;
        }

        return $transaction->balance_cents <= 0;
    }

    private function tagTransaction(
        TaggedTransactionData $taggedTransaction,
        UserBankTransactionRaw $rawTransaction,
        UserBankAccount $userBankAccount,
    ): void {
        $tag = TransactionTag::whereTag($taggedTransaction->tag)->first();

        if ($tag === null) {
            return;
        }

        $this->saveUserTransaction($rawTransaction, $userBankAccount, $tag);
    }

    private function saveUserTransaction(
        UserBankTransactionRaw $userBankTransactionRaw,
        UserBankAccount $userBankAccount,
        ?TransactionTag $tag = null,
        ?UserTransactionTag $userTransactionTag = null,
    ): void {
        $booked = $userBankTransactionRaw->booked_at ?? CarbonImmutable::now();

        UserTransaction::withoutGlobalScope(UserScope::class)->insertOrIgnore([
            'user_id' => $userBankAccount->user_id,
            'user_bank_account_id' => $userBankAccount->id,
            'transaction_tag_id' => $tag?->id,
            'user_transaction_tag_id' => $userTransactionTag?->id,
            'user_bank_transaction_raw_id' => $userBankTransactionRaw->id,
            'balance_cents' => $userBankTransactionRaw->balance_cents,
            'currency' => $userBankTransactionRaw->currency,
            'description' => $userBankTransactionRaw->remittance_information ?? $userBankTransactionRaw->additional_information,
            'booked_at' => $booked,
        ]);

        $tagId = $tag->id ?? 'other';
        $key = sprintf(CacheKeys::USER_TRANSACTIONS->value, $booked->format('m-Y'), $userBankAccount->user_id, $tagId);
        Cache::delete($key);
    }
}
