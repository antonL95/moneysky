<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class UserKrakenAccountData extends Data
{
    public function __construct(
        public int $id,
        public string $apiKey,
        public string $privateKey,
        public ?string $balance,
    ) {}
}
