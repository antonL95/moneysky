<?php

declare(strict_types=1);

namespace App\Crypto\Contracts;

use App\Crypto\DataTransferObjects\KrakenTickerPairDto;
use Illuminate\Support\Collection;

interface IExchangeClient
{
    /**
     * @return Collection<int, KrakenTickerPairDto>
     */
    public function fetchTickerPairsAndTradingValues(): Collection;

    public function fetchAccountBalance(string $apiKey, string $privateKey): int;
}
