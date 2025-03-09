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

        $previousSnapshot = UserPortfolioSnapshot::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('aggregate_date', $snapshot->aggregate_date->subDay()->toDateString())
            ->first();

        foreach (AssetType::cases() as $assetType) {
            $sum = $this->getSumOfAssets($user, $assetType);
            $totalSum += $sum;
            $percentageChange = 0;

            if ($previousSnapshot !== null) {
                $previousAssetSnapshot = $user->assetSnapshots()
                    ->withoutGlobalScopes()
                    ->where('snapshot_id', $previousSnapshot->id)
                    ->where('asset_type', $assetType->value)
                    ->first();

                if ($previousAssetSnapshot !== null && $previousAssetSnapshot->balance_cents !== 0) {
                    $percentageChange = (($sum - $previousAssetSnapshot->balance_cents) / $previousAssetSnapshot->balance_cents) * 100;
                }
            }

            $user->assetSnapshots()
                ->withoutGlobalScopes()
                ->updateOrCreate([
                    'asset_type' => $assetType->value,
                    'snapshot_id' => $snapshot->id,
                ], [
                    'balance_cents' => $sum,
                    'change' => $percentageChange,
                ]);
        }

        $percentageChange = 0;

        if ($previousSnapshot !== null && $previousSnapshot->balance_cents !== 0) {
            $percentageChange = (($totalSum - $previousSnapshot->balance_cents) / $previousSnapshot->balance_cents) * 100;
        }

        $snapshot->balance_cents = $totalSum;
        $snapshot->change = $percentageChange;
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
