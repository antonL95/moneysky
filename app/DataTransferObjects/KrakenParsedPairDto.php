<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class KrakenParsedPairDto
{
    public function __construct(
        public string $crypto,
        public string $fiat,
    ) {
    }
}
