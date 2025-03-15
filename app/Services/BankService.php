<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\App\BankAccount\BankInstitutionData;
use App\Data\GoCardless\AccountBalanceData;
use App\Data\GoCardless\AccountDetailsData;
use App\Data\GoCardless\AccountMetadataData;
use App\Data\GoCardless\AccountTransactionsData;
use App\Data\GoCardless\AgreementData;
use App\Data\GoCardless\InstitutionsData;
use App\Data\GoCardless\RequisitionAccountData;
use App\Data\GoCardless\RequisitionData;
use App\Data\GoCardless\SessionData;
use App\Enums\BankAccountStatus;
use App\Exceptions\InvalidApiExceptionAbstract;
use App\Exceptions\UserDoesNotHaveRequisition;
use App\Http\Integrations\GoCardless\GoCardlessConnector;
use App\Jobs\ProcessBankAccountsJob;
use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Sentry\Laravel\Integration;
use Spatie\LaravelData\Optional;

use function is_array;

final readonly class BankService
{
    public function __construct(
        private GoCardlessConnector $connector,
    ) {}

    /**
     * @return Collection<int, InstitutionsData>
     */
    public function getInstitutions(): Collection
    {
        /** @var Collection<int, InstitutionsData> $institutions */
        $institutions = $this->connector->institutions()
            ->retrieveAllSupportedInstitutionsInGivenCountry()->dto();

        return $institutions;
    }

    public function connect(BankInstitution $bankInstitution, User $user, ?UserBankSession $previousSession = null): string
    {
        $session = $this->createSession($bankInstitution, $previousSession);

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
    public function create(
        User $user,
        string $ref,
        ?UserBankSession $lastSession = null,
    ): void {
        $userBankSession = UserBankSession::with('bankInstitution')
            ->whereRequisitionId($ref)
            ->whereUserId($user->id)
            ->firstOrFail();

        /** @var RequisitionAccountData $accounts */
        $accounts = $this->connector->requisitions()
            ->requisitionById($userBankSession->requisition_id)
            ->dto();

        $agreementDays = 90;

        $from = CarbonImmutable::now()->subDays(
            min($userBankSession->bankInstitution->transaction_total_days ?? 30, 90),
        );

        foreach ($accounts->accounts as $id) {
            /** @var AccountMetadataData $accountMetadata */
            $accountMetadata = $this->connector->accounts()->retrieveAccountMetadata($id)->dto();
            /** @var AccountDetailsData $accountDetails */
            $accountDetails = $this->connector->accounts()->retrieveAccountDetails($id)->dto();

            if ($lastSession instanceof UserBankSession) {
                $userBankAccount = UserBankAccount::where('external_id', $accountMetadata->id)
                    ->first();

                if ($userBankAccount instanceof UserBankAccount) {
                    $userBankAccount->update([
                        'access_expires_at' => now()->addDays($agreementDays),
                        'status' => $accountMetadata->status,
                        'user_bank_session_id' => $userBankSession->id,
                    ]);

                    $updateFrom = $userBankAccount->updated_at ?? $from;

                    ProcessBankAccountsJob::dispatch(
                        $userBankAccount,
                        $updateFrom,
                        CarbonImmutable::now(),
                    );

                    continue;
                }
            }

            $userBankAccount = UserBankAccount::createOrFirst([
                'user_id' => $user->id,
                'user_bank_session_id' => $userBankSession->id,
                'external_id' => $accountMetadata->id,
                'resource_id' => $accountDetails->resourceId instanceof Optional
                    ? null
                    : $accountDetails->resourceId,
                'name' => $accountDetails->name instanceof Optional
                    ? sprintf(
                        '%s (%s)',
                        $userBankSession->bankInstitution?->name,
                        $accountDetails->currency,
                    ) : $accountDetails->name,
                'iban' => $accountDetails->iban instanceof Optional
                    ? null
                    : $accountDetails->iban,
                'balance_cents' => 0,
                'currency' => $accountDetails->currency,
                'access_expires_at' => now()->addDays($agreementDays),
                'status' => $accountMetadata->status,
            ]);

            ProcessBankAccountsJob::dispatch(
                $userBankAccount,
                $from,
                CarbonImmutable::now(),
            );
        }
    }

    /**
     * @throws InvalidApiExceptionAbstract
     */
    public function getAccountBalance(
        UserBankAccount $userBankAccount,
    ): AccountBalanceData {
        if ($userBankAccount->external_id === null) {
            throw InvalidApiExceptionAbstract::invalidConfiguration();
        }

        $accountBalances = $this->connector->accounts()
            ->retrieveAccountBalances($userBankAccount->external_id)
            ->array();

        if (! isset($accountBalances['balances']) || ! is_array($accountBalances['balances'])) {
            throw InvalidApiExceptionAbstract::noDataFound();
        }

        $balances = $accountBalances['balances'];

        $bankBalance = AccountBalanceData::from($balances[0]);

        foreach ($balances as $balance) {
            if (! is_array($balance)) {
                continue;
            }
            $bankBalance = AccountBalanceData::from($balance);
            if ($balance['balanceType'] === 'closingAvailable') {
                return $bankBalance;
            }
        }

        return $bankBalance;
    }

    /**
     * @return Collection<int, AccountTransactionsData>
     *
     * @throws InvalidApiExceptionAbstract
     */
    public function getAccountTransactions(
        UserBankAccount $userBankAccount,
        CarbonImmutable $from,
        CarbonImmutable $to,
    ): Collection {
        if ($userBankAccount->external_id === null) {
            throw InvalidApiExceptionAbstract::invalidConfiguration();
        }

        /** @var Collection<int, AccountTransactionsData> $transactions */
        $transactions = $this->connector->accounts()
            ->retrieveAccountTransactions(
                $userBankAccount->external_id,
                $from,
                $to,
            )->dto();

        return $transactions;
    }

    public function deleteUserRequisitions(User $user): void
    {
        $sessions = UserBankSession::withoutGlobalScopes()
            ->where('user_id', $user->id)->get();

        foreach ($sessions as $session) {
            $this->deleteRequisition($session);
        }
    }

    public function deleteRequisition(UserBankSession $userBankSession): void
    {
        try {
            $this->connector->requisitions()->deleteRequisitionById($userBankSession->requisition_id);
        } catch (Exception $e) {
            Integration::captureUnhandledException($e);
        }

        $userBankSession->delete();
    }

    /**
     * @throws InvalidApiExceptionAbstract
     */
    public function isAccountStatusReady(UserBankAccount $userBankAccount): void
    {
        /** @var AccountMetadataData $accountMetadata */
        $accountMetadata = $this->connector->accounts()
            ->retrieveAccountMetadata($userBankAccount->external_id)
            ->dto();

        if ($userBankAccount->status === $accountMetadata->status) {
            return;
        }

        if ($accountMetadata->status !== BankAccountStatus::READY) {
            $userBankAccount->status = $accountMetadata->status;
            $userBankAccount->save();

            throw InvalidApiExceptionAbstract::accountIsNotReady();
        }

        $userBankAccount->status = $accountMetadata->status;
        $userBankAccount->save();
    }

    public function createSession(
        BankInstitution $bankInstitution,
        ?UserBankSession $previousSession = null,
    ): SessionData {
        /** @var AgreementData $agreement */
        $agreement = $this->connector->agreements()->createEua(
            $bankInstitution->external_id,
            $bankInstitution->transaction_total_days,
        )->dto();

        /** @var RequisitionData $requisition */
        $requisition = $this->connector->requisitions()->createRequisition(
            $bankInstitution->external_id,
            $agreement->id,
            $previousSession instanceof UserBankSession
                ? route('bank-account.renew-callback', ['userBankSession' => $previousSession->id])
                : route('bank-account.callback'),
        )->dto();

        return new SessionData(
            $requisition->link,
            $requisition->id,
            $agreement->id,
        );
    }

    /**
     * @return array<int, array<int, BankInstitutionData>>
     */
    public function getActiveBankInstitutions(): array
    {
        /** @var Collection<int, BankInstitutionData> $institutions */
        $institutions = Cache::rememberForever(
            'bank-institutions',
            /** @returns Collection<int, BankInstitutionData> */
            static fn (): Collection => BankInstitution::whereActive(true)
                ->get()
                ->map(
                    fn (BankInstitution $bankInstitution): BankInstitutionData => new BankInstitutionData(
                        $bankInstitution->id,
                        $bankInstitution->name,
                        $bankInstitution->logo_url,
                        null,
                    ),
                ),
        );

        $result = [];

        foreach ($institutions as $institution) {
            $result[$institution->logo] = $institution;
        }

        /** @var Collection<string, BankInstitutionData> $institutions */
        $institutions = collect($result);

        /** @var array<int, array<int, BankInstitutionData>> $result */
        $result = $institutions->shuffle()->chunk((int) ceil($institutions->count() / 4))->toArray();

        return $result;
    }
}
