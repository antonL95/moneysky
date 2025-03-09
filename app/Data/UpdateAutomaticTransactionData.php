<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class UpdateAutomaticTransactionData extends Data
{
    public function __construct(
        public int $transactionTagId,
    ) {}
}
