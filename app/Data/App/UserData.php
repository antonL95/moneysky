<?php

declare(strict_types=1);

namespace App\Data\App;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $currency,
        public readonly bool $isSubscribed,
        public readonly bool $emailVerified,
    ) {}
}
