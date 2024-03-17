<?php

declare(strict_types=1);

namespace App\Bank\Jobs;

use App\Bank\DataTransferObjects\TaggedTransactionDto;
use App\Bank\Models\TransactionTag;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankTransactionRaw;
use App\Bank\Models\UserTransaction;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\OpenAi\Services\OpenAiService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected OpenAiService $openAiService;

    public const int MAX_DAYS = 2;

    public int $timeout = 900;

    public function __construct(
        public User $user,
        public Carbon $from,
        public Carbon $to,
    ) {
    }

    public function handle(OpenAiService $openAiService): void
    {
        $this->openAiService = $openAiService;
        $bankAccounts = UserBankAccount::withoutGlobalScope(UserScope::class)
            ->where('user_id', $this->user->id)->get();

        if ($bankAccounts->isEmpty()) {
            return;
        }

        foreach ($bankAccounts as $account) {
            $this->processTransactions($account);
        }
    }

    private function processTransactions(UserBankAccount $account): void
    {
        $transactions = UserBankTransactionRaw::where('user_bank_account_id', '=', $account->id)
            ->whereBetween(
                'booked_at',
                [$this->from, $this->to],
            )->get();

        foreach ($transactions as $transaction) {
            $shouldTag = $this->processTransaction($transaction);
            if ($shouldTag) {
                $taggedTransactions = $this->openAiService->classifyTransactions($transaction);
                if ($taggedTransactions !== []) {
                    $this->tagTransaction($taggedTransactions, $account);
                }
            }
        }
    }

    private function processTransaction(UserBankTransactionRaw $transaction): bool
    {
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

        // Find similar already tagged transactions
        $tenPercent = $transaction->balance_cents * 0.1;
        $similarTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
            ->where('user_id', $this->user->id)
            ->whereBetween('balance_cents', [$transaction->balance_cents - $tenPercent, $transaction->balance_cents + $tenPercent])
            ->where('currency', '=', $transaction->currency)
            ->where('description', 'like', '%'.$transaction->remittance_information.'%')
            ->first();

        if ($similarTransaction !== null) {
            UserTransaction::create([
                'user_id' => $this->user->id,
                'user_bank_account_id' => $transaction->user_bank_account_id,
                'transaction_tag_id' => $similarTransaction->transaction_tag_id,
                'user_transaction_tag_id' => $similarTransaction->user_transaction_tag_id,
                'balance_cents' => $transaction->balance_cents,
                'currency' => $transaction->currency,
                'description' => $transaction->remittance_information,
                'booked_at' => $transaction->booked_at,
            ]);

            return false;
        }

        return true;
    }

    /**
     * @param TaggedTransactionDto[] $taggedTransactions
     */
    private function tagTransaction(
        array $taggedTransactions,
        UserBankAccount $account,
    ): void {
        foreach ($taggedTransactions as $taggedTransaction) {
            $tag = TransactionTag::whereTag($taggedTransaction->tag)->first();

            if ($tag === null) {
                continue;
            }

            $rawTransaction = UserBankTransactionRaw::find($taggedTransaction->id);

            if ($rawTransaction === null) {
                continue;
            }

            UserTransaction::create([
                'user_id' => $this->user->id,
                'user_bank_account_id' => $account->id,
                'transaction_tag_id' => $tag->id,
                'user_bank_transaction_raw_id' => $rawTransaction->id,
                'balance_cents' => $rawTransaction->balance_cents,
                'currency' => $rawTransaction->currency,
                'description' => $rawTransaction->remittance_information ?? $rawTransaction->additional_information,
                'booked_at' => $rawTransaction->booked_at ?? Carbon::now(),
            ]);
        }
    }
}
