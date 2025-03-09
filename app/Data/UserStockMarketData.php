<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class UserStockMarketData extends Data
{
    public function __construct(
        public int $id,
        public string $ticker,
        public float|int|null $amount,
        public ?string $balance,
    ) {}
}
