<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Dto;

final class InstitutionsData extends Dto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $bic,
        public string $transaction_total_days,
        /** @var string[] $countries */
        public array $countries,
        public string $logo,
    ) {}
}
