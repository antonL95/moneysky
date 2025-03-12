<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Data\App\Dashboard\TransactionData;
use App\Enums\CacheKeys;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

final readonly class UpdateTransaction
{
    public function __construct(
        private AfterTransactionCreateOrUpdate $afterTransactionCreateOrUpdate,
    ) {}

    public function handle(User $user, UserTransaction $userTransaction, TransactionData $data): void
    {
        $balanceCents = abs($data->balance * 100);
        $balanceCentsBefore = $userTransaction->balance_cents * -1;

        $cacheKey = sprintf(
            CacheKeys::USER_TRANSACTIONS->value,
            $user->id,
            $userTransaction->transaction_tag_id ?? 'other',
            now()->format('Y-m-d'),
        );

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

        Cache::forget($cacheKey);
    }
}
