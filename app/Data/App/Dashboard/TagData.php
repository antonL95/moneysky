<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class TagData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}
}
