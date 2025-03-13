<?php

declare(strict_types=1);

namespace App\Data\App\StockMarket;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserStockMarketData extends Data
{
    public function __construct(
        public int $id,
        public string $ticker,
        public float|int|null $amount,
        public ?string $balance,
    ) {}
}
