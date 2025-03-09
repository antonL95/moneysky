<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class BankInstitutionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $logo,
        public ?string $countries,
    ) {}
}
