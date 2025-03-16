<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AssetData extends Data
{
    public function __construct(
        public readonly string $assetName,
        public readonly string $balance,
        public readonly float $balanceNumeric,
        public readonly string $color,
    ) {}
}
