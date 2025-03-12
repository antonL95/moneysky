<?php

declare(strict_types=1);

namespace App\Data\App\ManualEntry;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserManualEntryData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public ?string $balance,
        public int|float $amount,
        public string $currency,
    ) {}
}
