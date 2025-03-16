<?php

declare(strict_types=1);

namespace App\Data\App\Services;

use Spatie\LaravelData\Dto;

final class TaggedTransactionData extends Dto
{
    public function __construct(
        public int $id,
        public string $tag,
    ) {}
}
