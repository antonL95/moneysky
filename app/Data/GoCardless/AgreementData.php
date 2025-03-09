<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Data;

final class AgreementData extends Data
{
    public function __construct(
        public string $id,
    ) {}
}
