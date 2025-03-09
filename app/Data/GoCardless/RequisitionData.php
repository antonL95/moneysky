<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Dto;

final class RequisitionData extends Dto
{
    public function __construct(
        public string $id,
        public string $link,
    ) {}
}
