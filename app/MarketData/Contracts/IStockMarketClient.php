<?php

declare(strict_types=1);

namespace App\MarketData\Contracts;

interface IStockMarketClient
{
    public function fetchPriceForTicker(
        string $ticker,
    ): int;
}
