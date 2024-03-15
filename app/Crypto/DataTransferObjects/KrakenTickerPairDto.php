<?php

declare(strict_types=1);

namespace App\Crypto\DataTransferObjects;

readonly class KrakenTickerPairDto
{
    public function __construct(
        public string $pair,
        public string $crypto,
        public string $fiat,
        public int $tradeValue,
    ) {
    }
}
