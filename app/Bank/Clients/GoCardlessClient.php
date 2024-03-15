<?php

declare(strict_types=1);

namespace App\Bank\Clients;

use App\Bank\Contracts\IBankClient;
use App\Bank\DataTransferObjects\AuthenticationDto;
use App\Bank\DataTransferObjects\BankInstitutionDto;
use App\Bank\DataTransferObjects\UserAccountBalanceDto;
use App\Bank\DataTransferObjects\UserAccountTransactionsDto;
use App\Bank\DataTransferObjects\UserAgreementDto;
use App\Bank\DataTransferObjects\UserBankAccountDto;
use App\Bank\DataTransferObjects\UserRequisitionDto;
use App\Bank\Enums\BalanceType;
use App\Bank\Exceptions\AccountsNotFound;
use App\Bank\Exceptions\InvalidApiException;
use App\Bank\Models\BankInstitution;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankAgreement;
use App\Bank\Models\UserBankRequisition;
use App\Enums\CacheKeys;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

readonly class GoCardlessClient implements IBankClient
{
    private string $url;

    private string $secretKey;

    private string $secretId;

    public function __construct()
    {
        $url = Config::get('services.bank_data_api.url');
        $secretKey = Config::get('services.bank_data_api.secret_key');
        $secretId = Config::get('services.bank_data_api.secret_id');

        if (!\is_string($url) || !\is_string($secretKey) || !\is_string($secretId)) {
            throw InvalidApiException::invalidConfiguration();
        }

        $this->url = $url;
        $this->secretKey = $secretKey;
        $this->secretId = $secretId;
    }

    /**
     * @return Collection<int, BankInstitutionDto>
     */
    public function getInstitutions(): Collection
    {
        $data = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
            ->get(
                sprintf('%s/institutions/', $this->url),
            )
            ->json();
        $institutions = [];
        foreach ($data as $institution) {
            if (!\is_array($institution) || !isset($institution['id'], $institution['name'], $institution['bic'], $institution['transaction_total_days'], $institution['countries'], $institution['logo'])) {
                continue;
            }

            $institutions[] = BankInstitutionDto::fromArray($institution);
        }

        return collect($institutions);
    }

    /**
     * @throws InvalidApiException
     */
    public function createAgreement(
        BankInstitution $bankInstitution,
    ): UserAgreementDto {
        $data = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
            ->post(
                sprintf('%s/agreements/enduser/', $this->url),
                [
                    'institution_id' => $bankInstitution->external_id,
                    'max_historical_days' => $bankInstitution->transaction_total_days,
                    'access_valid_for_days' => 90,
                    'access_scope' => ['transactions', 'balances', 'details'],
                ],
            )
            ->json();

        /** @var array<string, string|int|string[]> $data */
        return UserAgreementDto::fromArray($data);
    }

    public function createRequisition(
        UserBankAgreement $userBankAgreement,
        BankInstitution $bankInstitution,
        ?UserSetting $userSetting = null,
    ): UserRequisitionDto {
        $data = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
            ->post(sprintf('%s/requisitions/', $this->url), [
                'agreement' => $userBankAgreement->external_id,
                'user_language' => $userSetting?->language ?? Config::get('services.locale'),
                'redirect' => route('app.bank-data-redirect'),
                'institution_id' => $bankInstitution->external_id,
            ])
            ->json();

        /** @var array<string, string> $data */
        return UserRequisitionDto::fromArray($data);
    }

    /**
     * @return Collection<int,UserBankAccountDto>
     *
     * @throws AccountsNotFound
     */
    public function getAccounts(UserBankRequisition $userBankRequisition): Collection
    {
        $accountsFromRequisition = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
            ->get(sprintf('%s/requisitions/%s/', $this->url, $userBankRequisition->external_id))
            ->json();

        $accounts = [];

        if (!isset($accountsFromRequisition['accounts']) || !\is_array($accountsFromRequisition['accounts'])) {
            throw new AccountsNotFound;
        }

        foreach ($accountsFromRequisition['accounts'] as $accountId) {
            $accountDetail = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
                ->get(sprintf('%s/accounts/%s/details', $this->url, $accountId))
                ->json();

            if (!isset($accountDetail['account']) || !\is_array($accountDetail['account'])) {
                continue;
            }

            /** @var array<string, string|int|string[]|null> $account */
            $account = $accountDetail['account'];
            $account['id'] = $accountId;

            $accounts[] = UserBankAccountDto::fromArray($account);
        }

        return collect($accounts);
    }

    /**
     * @throws InvalidApiException
     */
    public function getBalance(
        UserBankAccount $userBankAccount,
    ): UserAccountBalanceDto {
        $data = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
            ->get(sprintf('%s/accounts/%s/balances/', $this->url, $userBankAccount->external_id))
            ->json();

        if (!isset($data['balances'])) {
            throw InvalidApiException::createForAccountBalance();
        }

        if (!\is_array($data['balances'])) {
            throw InvalidApiException::createForAccountBalance();
        }

        foreach ($data['balances'] as $balance) {
            if (!\is_array($balance)) {
                continue;
            }

            if (
                \in_array($balance['balanceType'], [
                    BalanceType::CLOSING_AVAILABLE->value,
                    BalanceType::EXPECTED->value,
                    BalanceType::INTERIM_AVAILABLE->value,
                ], true)) {
                /** @var array<string, array<string, float>> $balance */
                return UserAccountBalanceDto::fromArray($balance);
            }
        }

        throw InvalidApiException::noDataFound();
    }

    /**
     * @return Collection<int, UserAccountTransactionsDto>
     *
     * @throws InvalidApiException
     */
    public function getTransactions(
        UserBankAccount $userBankAccount,
        Carbon $from,
        Carbon $to,
    ): Collection {
        $data = (array) Http::withHeader('Authorization', 'Bearer '.$this->getAuthToken())
            ->get(
                sprintf(
                    '%s/accounts/%s/transactions/?date_from=%s&date_to=%s',
                    $this->url,
                    $userBankAccount->external_id,
                    $from->toDateString(),
                    $to->toDateString(),
                ),
            )->json();

        if (!isset($data['transactions']) || !\is_array($data['transactions'])) {
            throw InvalidApiException::createForAccountTransactions();
        }

        if (!isset($data['transactions']['booked']) || !\is_array($data['transactions']['booked'])) {
            throw InvalidApiException::createForAccountTransactions();
        }

        $transactions = $data['transactions']['booked'];

        $temp = [];

        foreach ($transactions as $transaction) {
            if (!\is_array($transaction)) {
                continue;
            }

            $temp[] = UserAccountTransactionsDto::fromArray($transaction);
        }

        return collect($temp);
    }

    /**
     * @throws InvalidApiException
     */
    private function getAuthToken(): string
    {
        if (Cache::has(CacheKeys::BANK_DATA_API_ACCESS_TOKEN->value)) {
            $accessToken = Cache::get(CacheKeys::BANK_DATA_API_ACCESS_TOKEN->value);

            if (!\is_string($accessToken)) {
                throw InvalidApiException::invalidAccessToken();
            }

            return $accessToken;
        }

        $authData = $this->authenticateUsingRefresh();

        Cache::put(CacheKeys::BANK_DATA_API_ACCESS_TOKEN->value, $authData->access, $authData->accessExpires);

        if ($authData->refresh !== null && $authData->refreshExpires !== null) {
            Cache::put(CacheKeys::BANK_DATA_API_REFRESH_TOKEN->value, $authData->refresh, $authData->refreshExpires);
        }

        return $authData->access;
    }

    private function authenticate(): AuthenticationDto
    {
        $data = (array) Http::post(
            sprintf(
                '%s/token/new/',
                $this->url,
            ),
            [
                'secret_id' => $this->secretId,
                'secret_key' => $this->secretKey,
            ],
        )->json();

        /** @var array<string, string|int|null> $data */
        return AuthenticationDto::fromArray($data);
    }

    private function authenticateUsingRefresh(): AuthenticationDto
    {
        if (Cache::missing(CacheKeys::BANK_DATA_API_REFRESH_TOKEN->value)) {
            return $this->authenticate();
        }

        $data = (array) Http::post(
            sprintf(
                '%s/token/refresh/',
                $this->url,
            ),
            [
                'refresh' => Cache::get(CacheKeys::BANK_DATA_API_REFRESH_TOKEN->value),
            ],
        )->json();

        /** @var array<string, string|int|null> $data */
        return AuthenticationDto::fromArray($data);
    }
}
