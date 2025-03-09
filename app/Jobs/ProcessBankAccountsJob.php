<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\BankAccountStatus;
use App\Enums\ErrorCodes;
use App\Exceptions\InvalidApiExceptionAbstract;
use App\Models\UserBankAccount;
use App\Models\UserBankTransactionRaw;
use App\Services\BankService;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sentry\Laravel\Integration;

final class ProcessBankAccountsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    private BankService $bankService;

    public function __construct(
        public UserBankAccount $userBankAccount,
        public CarbonImmutable $from,
        public CarbonImmutable $to,
    ) {}

    public function handle(BankService $bankService): void
    {
        $this->bankService = $bankService;

        if ($this->userBankAccount->access_expires_at < now()) {
            $this->userBankAccount->status = BankAccountStatus::EXPIRED;
            $this->userBankAccount->save();

            return;
        }

        try {
            $this->bankService->isAccountStatusReady($this->userBankAccount);
        } catch (InvalidApiExceptionAbstract) {
            return;
        }

        $this->fetchBalance();
        $this->fetchTransaction();

        $user = $this->userBankAccount->user;

        if ($user === null) {
            return;
        }

        ProcessSnapshotJob::dispatch($user);
    }

    private function fetchBalance(): void
    {
        try {
            $balance = $this->bankService->getAccountBalance($this->userBankAccount);
        } catch (InvalidApiExceptionAbstract $e) {
            if ($e->getCode() === ErrorCodes::NO_DATA_FOUND->value) {
                return;
            }

            Integration::captureUnhandledException($e);

            return;
        }

        $this->userBankAccount->balance_cents = $balance->balance;
        $this->userBankAccount->save();
    }

    private function fetchTransaction(): void
    {
        try {
            $transactions = $this->bankService->getAccountTransactions($this->userBankAccount, $this->from, $this->to);
        } catch (InvalidApiExceptionAbstract $e) {
            if ($e->getCode() === ErrorCodes::NO_DATA_FOUND->value) {
                return;
            }

            Integration::captureUnhandledException($e);

            return;
        }

        $temp = [];

        if ($transactions->isEmpty()) {
            return;
        }

        foreach ($transactions as $transaction) {
            $temp[] = [
                'user_bank_account_id' => $this->userBankAccount->id,
                'external_id' => $transaction->entryReference ?? $transaction->internalTransactionId,
                'balance_cents' => $transaction->balance,
                'currency' => $transaction->currency,
                'currency_exchange' => json_encode($transaction->currencyExchange, JSON_THROW_ON_ERROR),
                'additional_information' => $transaction->additionalInformation,
                'remittance_information' => $transaction->remittanceInformationUnstructured,
                'booked_at' => $transaction->bookingTime ?? $transaction->booking ?? null,
                'merchant_category_code' => $transaction->merchantCategoryCode ?? null,
            ];
        }

        UserBankTransactionRaw::insertOrIgnore($temp);
    }
}
