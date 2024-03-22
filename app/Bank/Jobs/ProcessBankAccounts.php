<?php

declare(strict_types=1);

namespace App\Bank\Jobs;

use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankTransactionRaw;
use App\Bank\Services\BankService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function Safe\json_encode;

class ProcessBankAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Carbon $from;

    public Carbon $to;

    protected BankService $bankService;

    public function __construct(
        public UserBankAccount $userBankAccount,
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

        $this->fetchBalance();
        $this->fetchTransaction();
    }

    private function fetchBalance(): void
    {
        $balance = $this->bankService->getAccountBalance($this->userBankAccount);

        $this->userBankAccount->balance_cents = $balance->balance;
        $this->userBankAccount->save();
    }

    private function fetchTransaction(): void
    {
        $transactions = $this->bankService->getAccountTransactions($this->userBankAccount, $this->from, $this->to);

        $temp = [];

        if ($transactions->isEmpty()) {
            return;
        }

        foreach ($transactions as $transaction) {
            $temp[] = [
                'user_bank_account_id' => $this->userBankAccount->id,
                'external_id' => $transaction->externalId,
                'balance_cents' => $transaction->balance,
                'currency' => $transaction->currency,
                'currency_exchange' => json_encode($transaction->currencyExchange),
                'additional_information' => $transaction->additionalInformation,
                'remittance_information' => $transaction->remittanceInformation,
                'booked_at' => $transaction->bookingDateTime ?? $transaction->bookingDate ?? null,
                'merchant_category_code' => $transaction->merchantCategoryCode ?? null,
            ];
        }

        UserBankTransactionRaw::insertOrIgnore($temp);
    }
}
