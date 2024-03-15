<?php

declare(strict_types=1);

namespace App\Bank\Services;

use App\Bank\Contracts\IBankClient;
use App\Bank\Exceptions\UserDoesNotHaveRequisition;
use App\Bank\Jobs\ProcessBankAccounts;
use App\Bank\Models\BankInstitution;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankAgreement;
use App\Bank\Models\UserBankRequisition;
use App\Models\User;
use Illuminate\Support\Facades\Config;

readonly class BankAccounts
{
    public function __construct(
        private IBankClient $bankClient,
    ) {
    }

    public function connect(BankInstitution $bankInstitution, User $user): string
    {
        $userAgreement = $this->bankClient->createAgreement($bankInstitution);

        $agreement = UserBankAgreement::create([
            'user_id' => $user->id,
            'bank_institution_id' => $bankInstitution->id,
            'external_id' => $userAgreement->id,
            'access_valid_for_days' => $userAgreement->accessValidForDays,
            'max_historical_days' => $userAgreement->maxHistoricalDays,
            'access_scope' => $userAgreement->accessScope,
            'accepted_at' => now(),
        ]);

        $userSetting = $user->settings()->where('key', '=', 'language')->first();

        $userRequisition = $this->bankClient->createRequisition($agreement, $bankInstitution, $userSetting);

        $requisition = UserBankRequisition::create([
            'user_id' => $user->id,
            'user_bank_agreement_id' => $agreement->id,
            'bank_institution_id' => $bankInstitution->id,
            'external_id' => $userRequisition->id,
            'status' => $userRequisition->status,
            'link' => $userRequisition->link,
            'user_language' => $userSetting?->value ?? Config::get('services.locale'),
        ]);

        return $requisition->link;
    }

    /**
     * @throws UserDoesNotHaveRequisition
     */
    public function create(User $user, string $ref): void
    {
        $requisition = UserBankRequisition::with('userBankAgreement')
            ->where('external_id', '=', $ref)
            ->first();

        if ($requisition === null) {
            throw new UserDoesNotHaveRequisition;
        }

        $accounts = $this->bankClient->getAccounts($requisition);

        $from = now()->subDays($requisition->userBankAgreement->max_historical_days ?? 7);

        foreach ($accounts as $account) {
            $bankAccount = UserBankAccount::create([
                'user_id' => $user->id,
                'external_id' => $account->id,
                'name' => $account->name ?? $requisition->bankInstitution?->name.' account',
                'currency' => $account->currency,
                'type' => $account->type,
                'status' => $account->status,
            ]);

            ProcessBankAccounts::dispatch($bankAccount, $from, now());
        }
    }
}
