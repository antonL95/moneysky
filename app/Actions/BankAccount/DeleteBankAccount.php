<?php

declare(strict_types=1);

namespace App\Actions\BankAccount;

use App\Jobs\ProcessSnapshotJob;
use App\Models\UserBankAccount;

final readonly class DeleteBankAccount
{
    public function handle(UserBankAccount $bankAccount): void
    {
        $user = $bankAccount->user;
        $bankAccount->delete();
        ProcessSnapshotJob::dispatch($user);
    }
}
