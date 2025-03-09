<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class SpendingData extends Data
{
    public function __construct(
        public string $value,
        public float|int $amount,
        public string|int|null $tagId,
    ) {}
}
