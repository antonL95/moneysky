<?php

declare(strict_types=1);

use App\Actions\BankAccount\UpdateBankAccount;
use App\Data\App\BankAccount\BankAccountData;
use App\Models\UserBankAccount;

use function Pest\Laravel\assertDatabaseHas;

it('successfully updates', function () {
    $bankAccount = UserBankAccount::factory()->create();

    assertDatabaseHas('user_bank_accounts', [
        'id' => $bankAccount->id,
        'name' => $bankAccount->name,
    ]);

    app(UpdateBankAccount::class)->handle($bankAccount, new BankAccountData(
        'New Name',
    ));
    expect($bankAccount->fresh()->name)->toBe('New Name');
});
