<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class TransactionAggregateData extends Data
{
    public function __construct(
        public string $name,
        public string $value,
        public float|int $amount,
        public string|int|null $tagId,
    ) {}
}
