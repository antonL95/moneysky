<?php

declare(strict_types=1);

namespace App\Crypto\DataTransferObjects;

readonly class CryptoQuoteDto
{
    public function __construct(
        public string $symbol,
        public int $quoteCents,
    ) {
    }
}
