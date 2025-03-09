<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Dto;
use Spatie\LaravelData\Optional;

final class AccountDetailsData extends Dto
{
    public function __construct(
        public string $currency,
        public string|Optional $name,
        public string|Optional $iban,
        public string|Optional $resourceId,
        public string|Optional $product,
    ) {}
}
