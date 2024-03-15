<?php

declare(strict_types=1);

namespace App\Bank\Contracts;

use App\Bank\DataTransferObjects\BankInstitutionDto;
use App\Bank\DataTransferObjects\UserAccountBalanceDto;
use App\Bank\DataTransferObjects\UserAccountTransactionsDto;
use App\Bank\DataTransferObjects\UserAgreementDto;
use App\Bank\DataTransferObjects\UserBankAccountDto;
use App\Bank\DataTransferObjects\UserRequisitionDto;
use App\Bank\Models\BankInstitution;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankAgreement;
use App\Bank\Models\UserBankRequisition;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface IBankClient
{
    /**
     * @return Collection<int, BankInstitutionDto>
     */
    public function getInstitutions(): Collection;

    public function createAgreement(BankInstitution $bankInstitution): UserAgreementDto;

    public function createRequisition(
        UserBankAgreement $userBankAgreement,
        BankInstitution $bankInstitution,
        ?UserSetting $userSetting,
    ): UserRequisitionDto;

    /**
     * @return Collection<int, UserBankAccountDto>
     */
    public function getAccounts(UserBankRequisition $userBankRequisition): Collection;

    public function getBalance(UserBankAccount $userBankAccount): UserAccountBalanceDto;

    /**
     * @return Collection<int, UserAccountTransactionsDto>
     */
    public function getTransactions(
        UserBankAccount $userBankAccount,
        Carbon $from,
        Carbon $to,
    ): Collection;
}
