<?php

declare(strict_types=1);

use App\Data\App\Dashboard\AssetData;
use App\Data\App\Dashboard\HistoricalAssetsData;
use App\Enums\AssetType;
use App\Services\AssetsService;
use Database\Seeders\TestSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(TestSeeder::class);

    $this->user = App\Models\User::find(1);
});

it('returns historical data', function () {
    $service = app(AssetsService::class);
    actingAs($this->user);

    $results = $service->getHistoricalData($this->user);

    expect(count($results))
        ->toBe(count(AssetType::cases()) + 1)
        ->and($results[0]->assetsData->count())->toBe(30);

    collect($results)->each(function (HistoricalAssetsData $asset) {
        expect($asset->assetsData->count())->toBe(30);
    });
});

it('returns latest assets breakdown', function () {
    $service = app(AssetsService::class);
    actingAs($this->user);

    $results = $service->getAssets($this->user);

    expect(count($results))
        ->toBe(count(AssetType::cases()))
        ->and($results[0]->balanceNumeric)->toBeGreaterThan(0);

    collect($results)->each(function (AssetData $asset) {
        expect($asset->balanceNumeric)->toBeGreaterThan(0);
    });
});

it('returns latest total assets breakdown', function () {
    $service = app(AssetsService::class);
    actingAs($this->user);

    $results = $service->getTotalAsset($this->user);

    expect($results->balanceNumeric)->toBeGreaterThan(0);
});
