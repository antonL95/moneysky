<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Data;

final class RequisitionAccountData extends Data
{
    /**
     * @param  string[]  $accounts
     */
    public function __construct(
        public array $accounts,
    ) {}
}
