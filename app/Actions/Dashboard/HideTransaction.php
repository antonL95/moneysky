<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Models\User;
use App\Models\UserTransaction;

final readonly class HideTransaction
{
    public function __construct(
        private AfterTransactionCreateOrUpdate $afterTransactionCreateOrUpdate,
    ) {}

    public function handle(User $user, UserTransaction $transaction): void
    {
        $transaction->update([
            'hidden' => true,
        ]);

        $this->afterTransactionCreateOrUpdate->dispatchJobs(
            $user,
            $transaction,
            $transaction->booked_at,
        );
    }
}
