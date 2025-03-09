<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Dto;

final class SessionData extends Dto
{
    public function __construct(
        public string $link,
        public string $requisition_id,
        public string $agreement_id,
    ) {}
}
