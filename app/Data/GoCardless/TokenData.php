<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Dto;

final class TokenData extends Dto
{
    public function __construct(
        public string $access,
        public int $access_expires,
        public string $refresh,
        public int $refresh_expires,
    ) {}
}
