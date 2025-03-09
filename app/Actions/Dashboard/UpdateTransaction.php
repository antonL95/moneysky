<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Data\TransactionData;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\CarbonImmutable;

final readonly class UpdateTransaction
{
    public function __construct(
        private AfterTransactionCreateOrUpdate $afterTransactionCreateOrUpdate,
    ) {}

    public function handle(User $user, UserTransaction $userTransaction, TransactionData $data): void
    {
        $balanceCents = abs($data->balance * 100);
        $balanceCentsBefore = $userTransaction->balance_cents * -1;

        $updateData = [
            'transaction_tag_id' => $data->transaction_tag_id,
        ];

        if ($userTransaction->user_bank_account_id === null) {
            $updateData += [
                'balance_cents' => ((int) $balanceCents) * -1,
                'currency' => $data->currency,
                'user_manual_entry_id' => $data->user_manual_entry_id,
                'description' => $data->description,
            ];
        }

        $userTransaction->update($updateData);

        $this->afterTransactionCreateOrUpdate->handle(
            $user,
            $userTransaction,
            (int) ($balanceCentsBefore - $balanceCents),
            CarbonImmutable::now(),
        );
    }
}
