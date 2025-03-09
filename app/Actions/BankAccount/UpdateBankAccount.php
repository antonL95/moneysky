<?php

declare(strict_types=1);

namespace App\Actions\BankAccount;

use App\Data\BankAccountData;
use App\Models\UserBankAccount;

final readonly class UpdateBankAccount
{
    public function handle(UserBankAccount $bankAccount, BankAccountData $data): void
    {
        $bankAccount->update([
            'name' => $data->name,
        ]);
    }
}
