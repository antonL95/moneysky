<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\BankAccountDto;
use App\DataTransferObjects\BankBalanceDto;
use App\DataTransferObjects\BankInstitutionDto;
use App\DataTransferObjects\BankTransactionsDto;
use App\DataTransferObjects\SessionDto;
use App\Exceptions\InvalidApiException;
use App\Exceptions\UserDoesNotHaveRequisition;
use App\Jobs\ProcessBankAccounts;
use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Nordigen\NordigenPHP\API\NordigenClient;
use Nordigen\NordigenPHP\Exceptions\NordigenExceptions\NordigenException;

class BankService
{
    private NordigenClient $client;

    /**
     * @throws InvalidApiException
     */
    public function __construct()
    {
        $secretKey = Config::get('services.bank_data_api.secret_key');
        $secretId = Config::get('services.bank_data_api.secret_id');

        if (!\is_string($secretKey) || !\is_string($secretId)) {
            throw InvalidApiException::invalidConfiguration();
        }

        $this->client = new NordigenClient($secretId, $secretKey);
    }

    /**
     * @return Collection<int, BankInstitutionDto>
     */
    public function getInstitutions(): Collection
    {
        $this->client->createAccessToken();
        $institutions = $this->client->institution->getInstitutions();

        $temp = [];

        foreach ($institutions as $institution) {
            $temp[] = BankInstitutionDto::fromArray($institution);
        }

        return collect($temp);
    }

    public function connect(BankInstitution $bankInstitution, User $user): string
    {
        $this->client->createAccessToken();
        $session = $this->getSessionData($bankInstitution);

        UserBankSession::create([
            'user_id' => $user->id,
            'link' => $session->link,
            'bank_institution_id' => $bankInstitution->id,
            'agreement_id' => $session->agreement_id,
            'requisition_id' => $session->requisition_id,
        ]);

        return $session->link;
    }

    /**
     * @throws UserDoesNotHaveRequisition
     */
    public function create(User $user, string $ref): void
    {
        $this->client->createAccessToken();
        $userBankSession = UserBankSession::with('bankInstitution')
            ->whereRequisitionId($ref)
            ->first();

        if ($userBankSession === null) {
            throw new UserDoesNotHaveRequisition;
        }

        $accounts = $this->client->requisition->getRequisition($userBankSession->requisition_id)['accounts'];
        $agreementDays = $this->client->endUserAgreement->getEndUserAgreement($userBankSession->agreement_id)['access_valid_for_days'] ?? null;

        if (!\is_int($agreementDays)) {
            $agreementDays = 90;
        }

        $from = Carbon::now()->subDays($userBankSession->bankInstitution->transaction_total_days ?? 30);

        foreach ($accounts as $id) {
            $account = $this->client->account($id);
            $accountDetail = $account->getAccountDetails()['account'];
            $accountDetail['id'] = $id;
            $detail = BankAccountDto::fromArray($accountDetail);

            $userBankAccount = UserBankAccount::createOrFirst([
                'user_id' => $user->id,
                'user_bank_session_id' => $userBankSession->id,
                'external_id' => $detail->id,
                'resource_id' => $detail->resourceId,
                'name' => $detail->name ?? sprintf(
                    '%s (%s)',
                    $userBankSession->bankInstitution?->name,
                    $detail->currency,
                ),
                'iban' => $detail->iban,
                'balance_cents' => 0,
                'currency' => $detail->currency,
                'access_expires_at' => now()->addDays($agreementDays),
            ]);

            ProcessBankAccounts::dispatch($userBankAccount, $from, now());
        }
    }

    /**
     * @throws InvalidApiException
     */
    public function getAccountBalance(
        UserBankAccount $userBankAccount,
    ): BankBalanceDto {
        $this->client->createAccessToken();
        if ($userBankAccount->external_id === null) {
            throw InvalidApiException::invalidConfiguration();
        }

        $account = $this->client->account($userBankAccount->external_id);

        try {
            $accountBalances = $account->getAccountBalances();
        } catch (NordigenException) {
            throw InvalidApiException::noDataFound();
        }

        if (!isset($accountBalances['balances'])) {
            throw InvalidApiException::noDataFound();
        }

        $balances = $accountBalances['balances'];

        $bankBalance = BankBalanceDto::fromArray($balances[0]);

        foreach ($balances as $balance) {
            $bankBalance = BankBalanceDto::fromArray($balance);
            if ($balance['balanceType'] === 'closingAvailable') {
                return $bankBalance;
            }
        }

        return $bankBalance;
    }

    /**
     * @return Collection<int, BankTransactionsDto>
     *
     * @throws InvalidApiException
     */
    public function getAccountTransactions(
        UserBankAccount $userBankAccount,
        Carbon $from,
        Carbon $to,
    ): Collection {
        $this->client->createAccessToken();
        if ($userBankAccount->external_id === null) {
            throw InvalidApiException::invalidConfiguration();
        }

        try {
            $transactions = $this->client->account($userBankAccount->external_id)
                ->getAccountTransactions(
                    $from->toDateString(),
                    $to->toDateString(),
                );
        } catch (NordigenException) {
            return new Collection;
        }

        if (!isset($transactions['transactions']['booked'])) {
            return collect();
        }

        $booked = $transactions['transactions']['booked'];

        $temp = [];
        foreach ($booked as $transaction) {
            $temp[] = BankTransactionsDto::fromArray($transaction);
        }

        return collect($temp);
    }

    public function deleteUserRequisitions(User $user): void
    {
        $sessions = UserBankSession::withoutGlobalScopes()
            ->where('user_id', $user->id)->get();

        foreach ($sessions as $session) {
            try {
                $this->client->requisition->deleteRequisition($session->requisition_id);
            } catch (\Exception) {
            }

            $session->delete();
        }
    }

    public function deleteNotUsedRequisition(UserBankSession $userBankSession): void
    {
        $this->client->requisition->deleteRequisition($userBankSession->requisition_id);

        $userBankSession->delete();
    }

    private function getSessionData(
        BankInstitution $bankInstitution,
    ): SessionDto {
        $this->client->createAccessToken();
        $sessionData = $this->client->initSession(
            $bankInstitution->external_id,
            route('app.bank-data-redirect'),
            $bankInstitution->transaction_total_days,
        );

        return SessionDto::fromArray($sessionData);
    }
}
