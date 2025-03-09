<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\App\Dashboard\AssetData;
use App\Data\App\Dashboard\HistoricalAssetData;
use App\Data\App\Dashboard\HistoricalAssetsData;
use App\Enums\AssetType;
use App\Models\User;
use App\Models\UserPortfolioAsset;
use App\Models\UserPortfolioSnapshot;
use Illuminate\Database\Eloquent\Collection;

final readonly class AssetsService
{
    /**
     * @return HistoricalAssetsData[]
     */
    public function getHistoricalData(User $user, int $limit = 30): array
    {
        $results = [];

        /** @var Collection<int, UserPortfolioSnapshot> $snapshots */
        $snapshots = $user->dailySnapshots()
            ->with('assetSnapshots')
            ->latest('id')
            ->limit($limit)
            ->get();

        $results[] = new HistoricalAssetsData(
            assetName: 'Total Assets',
            color: '#eab308',
            assetsData: $snapshots->map(fn (UserPortfolioSnapshot $assetSnapshot): HistoricalAssetData => new HistoricalAssetData(
                date: $assetSnapshot->aggregate_date->format('Y-m-d'),
                balance: $assetSnapshot->balance,
                balanceNumeric: $assetSnapshot->balance_numeric,
            )),
        );

        foreach (AssetType::cases() as $type) {
            $assetSnapshots = $user->assetSnapshots()
                ->with('snapshot')
                ->where('asset_type', $type)
                ->latest()
                ->limit($limit)
                ->get();

            $results[] = new HistoricalAssetsData(
                assetName: $type->label(),
                color: $type->color(),
                assetsData: $assetSnapshots->map(fn (UserPortfolioAsset $assetSnapshot): HistoricalAssetData => new HistoricalAssetData(
                    date: $assetSnapshot->snapshot?->aggregate_date->format('Y-m-d') ?? now()->format('Y-m-d'),
                    balance: $assetSnapshot->balance,
                    balanceNumeric: $assetSnapshot->balance_numeric,
                )),
            );
        }

        return $results;
    }

    /**
     * @return AssetData[]
     */
    public function getAssets(User $user): array
    {
        $assetsData = [];

        /** @var UserPortfolioSnapshot $snapshot */
        $snapshot = $user->dailySnapshots()
            ->with('assetSnapshots')
            ->latest()
            ->first();

        foreach (AssetType::cases() as $type) {
            $snapshot->assetSnapshots->filter(fn (UserPortfolioAsset $assetSnapshot): bool => $assetSnapshot->asset_type === $type)
                ->each(function (UserPortfolioAsset $assetSnapshot) use (&$assetsData): void {
                    $assetsData[] = new AssetData(
                        assetName: $assetSnapshot->asset_type->label(),
                        balance: $assetSnapshot->balance,
                        balanceNumeric: $assetSnapshot->balance_numeric,
                        color: $assetSnapshot->asset_type->color(),
                    );
                });
        }

        return $assetsData;
    }

    public function getTotalAsset(User $user): AssetData
    {
        /** @var UserPortfolioSnapshot $snapshot */
        $snapshot = $user->dailySnapshots()
            ->with('assetSnapshots')
            ->latest()
            ->first();

        return new AssetData(
            'Total Assets',
            $snapshot->balance,
            $snapshot->balance_numeric,
            '#eab308',
        );
    }
}
