<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class HistoricalAssetsData extends Data
{
    public function __construct(
        public readonly string $assetName,
        public readonly string $color,
        /** @var Collection<int, HistoricalAssetData> $assetsData */
        public readonly Collection $assetsData,
    ) {}
}
