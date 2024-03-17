<?php

declare(strict_types=1);

namespace App\Bank\Jobs;

use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankTransactionRaw;
use App\Bank\Services\BankService;
use App\Models\Scopes\UserScope;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBankAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Carbon $from;

    public Carbon $to;

    protected BankService $bankService;

    public function __construct(
        public User $user,
        ?Carbon $from,
        ?Carbon $to,
    ) {
        if ($from === null) {
            $this->from = Carbon::now()
                ->setTime(0, 0)
                ->subDays();
        } else {
            $this->from = $from;
        }

        if ($to === null) {
            $this->to = Carbon::now()
                ->setTime(0, 0);
        } else {
            $this->to = $to;
        }
    }

    public function handle(BankService $bankService): void
    {
        $this->bankService = $bankService;
        $bankAccounts = UserBankAccount::withoutGlobalScope(UserScope::class)
            ->where('user_id', $this->user->id)->get();

        if ($bankAccounts->isEmpty()) {
            return;
        }

        foreach ($bankAccounts as $account) {
            $this->fetchBalance($account);
            $this->fetchTransaction($account);
        }

        $diff = $this->from->diffInDays($this->to);
        if ($diff > ProcessTransactionsJob::MAX_DAYS) {
            $chunks = ceil($diff / ProcessTransactionsJob::MAX_DAYS);

            for ($i = 0; $i < $chunks; ++$i) {
                $from = $this->from->copy()->addDays($i * ProcessTransactionsJob::MAX_DAYS);
                $to = $this->from->copy()->addDays(($i + 1) * ProcessTransactionsJob::MAX_DAYS);
                ProcessTransactionsJob::dispatch($this->user, $from, $to);
            }
        } else {
            ProcessTransactionsJob::dispatch($this->user, $this->from, $this->to);
        }
    }

    private function fetchBalance(UserBankAccount $account): void
    {
        $balance = $this->bankService->getAccountBalance($account);

        $account->balance_cents = $balance->balance;
        $account->save();
    }

    private function fetchTransaction(UserBankAccount $account): void
    {
        $transactions = $this->bankService->getAccountTransactions($account, $this->from, $this->to);

        $temp = [];

        if ($transactions->isEmpty()) {
            return;
        }

        foreach ($transactions as $transaction) {
            $temp[] = [
                'user_bank_account_id' => $account->id,
                'external_id' => $transaction->externalId,
                'balance_cents' => $transaction->balance,
                'currency' => $transaction->currency,
                'currency_exchange' => $transaction->currencyExchange,
                'additional_information' => $transaction->additionalInformation,
                'remittance_information' => $transaction->remittanceInformation,
                'booked_at' => $transaction->bookingDateTime ?? $transaction->bookingDate ?? null,
            ];
        }

        UserBankTransactionRaw::insertOrIgnore($temp);
    }
}
