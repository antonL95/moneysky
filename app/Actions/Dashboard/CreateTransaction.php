<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Data\App\Dashboard\TransactionData;
use App\Models\User;
use Carbon\CarbonImmutable;

final readonly class CreateTransaction
{
    public function __construct(
        private AfterTransactionCreateOrUpdate $afterTransactionCreateOrUpdate,
    ) {}

    public function handle(User $user, TransactionData $data): void
    {
        $balanceCents = abs($data->balance * 100);

        $now = CarbonImmutable::now();
        $userTransaction = $user->userTransaction()->create(
            [
                'transaction_tag_id' => $data->transaction_tag_id,
                'description' => $data->description,
                'balance_cents' => ((int) $balanceCents) * -1,
                'currency' => $data->currency,
                'booked_at' => $now,
                'user_manual_entry_id' => $data->user_manual_entry_id,
            ],
        );

        $this->afterTransactionCreateOrUpdate->handle($user, $userTransaction, (int) $balanceCents, $now);
    }
}
