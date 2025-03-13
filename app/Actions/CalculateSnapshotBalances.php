<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AssetType;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserCryptoWallet;
use App\Models\UserKrakenAccount;
use App\Models\UserManualEntry;
use App\Models\UserPortfolioSnapshot;
use App\Models\UserStockMarket;

final readonly class CalculateSnapshotBalances
{
    public function handle(UserPortfolioSnapshot $snapshot, User $user): void
    {
        $totalSum = 0;

        foreach (AssetType::cases() as $assetType) {
            $sum = $this->getSumOfAssets($user, $assetType);
            $totalSum += $sum;

            $user->assetSnapshots()
                ->withoutGlobalScopes()
                ->updateOrCreate([
                    'asset_type' => $assetType->value,
                    'snapshot_id' => $snapshot->id,
                ], [
                    'balance_cents' => $sum,
                    'change' => 0,
                ]);
        }

        $snapshot->balance_cents = $totalSum;
        $snapshot->change = 0;
        $snapshot->save();
    }

    private function getSumOfAssets(User $user, AssetType $assetType): int
    {
        return match ($assetType) {
            AssetType::CRYPTO => UserCryptoWallet::getSumOfUserWallets($user, true),
            AssetType::STOCK_MARKET => UserStockMarket::getSumOfAllTickers($user, true),
            AssetType::EXCHANGE => UserKrakenAccount::getSumOfAllAccounts($user, true),
            AssetType::MANUAL_ENTRIES => UserManualEntry::getSumWithCurrency($user, true),
            AssetType::BANK_ACCOUNTS => UserBankAccount::getSumOfAllUserBankAccounts($user, true),
        };
    }
}
