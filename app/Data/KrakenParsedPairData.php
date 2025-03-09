<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Dto;

final class KrakenParsedPairData extends Dto
{
    public function __construct(
        public string $crypto,
        public string $fiat,
    ) {}
}
