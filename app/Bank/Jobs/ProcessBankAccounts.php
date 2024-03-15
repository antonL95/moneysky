<?php

declare(strict_types=1);

namespace App\Bank\Jobs;

use App\Bank\Contracts\IBankClient;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankTransactionRaw;
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

    protected IBankClient $bankClient;

    public function __construct(
        public UserBankAccount $bankAccount,
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

    public function handle(IBankClient $bankClient): void
    {
        $this->bankClient = $bankClient;

        $this->processBalance();
        $this->processTransaction();
    }

    private function processBalance(): void
    {

        $balance = $this->bankClient->getBalance($this->bankAccount);

        $this->bankAccount->balance_cents = $balance->balance;
        $this->bankAccount->save();
    }

    private function processTransaction(): void
    {
        $transactions = $this->bankClient->getTransactions($this->bankAccount, $this->from, $this->to);

        $temp = [];
        foreach ($transactions as $transaction) {
            $temp[] = [
                'user_bank_account_id' => $this->bankAccount->id,
                'external_id' => $transaction->externalId,
                'balance_cents' => $transaction->balance,
                'currency' => $transaction->currency,
                'currency_exchange' => $transaction->currencyExchange,
                'additional_information' => $transaction->additionalInformation,
                'remittance_information' => $transaction->remittanceInformation,
            ];
        }

        UserBankTransactionRaw::insertOrIgnore($temp);
    }
}
