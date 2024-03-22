<?php

declare(strict_types=1);

namespace App\Bank\Jobs;

use App\Bank\DataTransferObjects\TaggedTransactionDto;
use App\Bank\Models\TransactionTag;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankTransactionRaw;
use App\Bank\Models\UserTransaction;
use App\Bank\Models\UserTransactionTag;
use App\Models\Scopes\UserScope;
use App\OpenAi\Exceptions\OpenAiExceptions;
use App\OpenAi\Services\OpenAiService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    protected OpenAiService $openAiService;

    protected Logger $logger;

    public int $timeout = 900;

    /**
     * @param Collection<int, UserBankTransactionRaw> $rawTransactionsToProcess
     */
    public function __construct(
        public Collection $rawTransactionsToProcess,
    ) {
    }

    public function handle(OpenAiService $openAiService, Logger $logger): void
    {
        $this->openAiService = $openAiService;
        $this->logger = $logger;

        $this->processTransactions();
    }

    private function processTransactions(): void
    {
        foreach ($this->rawTransactionsToProcess as $transaction) {
            $userBankAccount = UserBankAccount::withoutGlobalScope(UserScope::class)->find($transaction->user_bank_account_id);

            if ($userBankAccount === null) {
                continue;
            }

            $shouldTag = $this->processTransaction($transaction, $userBankAccount);

            if ($shouldTag) {
                try {
                    $taggedTransaction = $this->openAiService->classifyTransactions($transaction);
                    $this->tagTransaction($taggedTransaction, $transaction, $userBankAccount);
                } catch (OpenAiExceptions $e) {
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

        if ($transaction->balance_cents > 0) {
            return false;
        }

        return true;
    }

    private function tagTransaction(
        TaggedTransactionDto $taggedTransaction,
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
        UserTransaction::withoutGlobalScope(UserScope::class)->insertOrIgnore([
            'user_id' => $userBankAccount->user_id,
            'user_bank_account_id' => $userBankAccount->id,
            'transaction_tag_id' => $tag?->id,
            'user_transaction_tag_id' => $userTransactionTag?->id,
            'user_bank_transaction_raw_id' => $userBankTransactionRaw->id,
            'balance_cents' => $userBankTransactionRaw->balance_cents,
            'currency' => $userBankTransactionRaw->currency,
            'description' => $userBankTransactionRaw->remittance_information ?? $userBankTransactionRaw->additional_information,
            'booked_at' => $userBankTransactionRaw->booked_at ?? Carbon::now(),
        ]);
    }
}
