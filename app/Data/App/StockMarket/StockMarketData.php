<?php

declare(strict_types=1);

namespace App\Data\App\StockMarket;

use Spatie\LaravelData\Attributes\Validation\GreaterThan;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class StockMarketData extends Data
{
    public function __construct(
        public string $ticker,
        #[GreaterThan('0.0001')]
        public float|int $amount,
    ) {}
}
