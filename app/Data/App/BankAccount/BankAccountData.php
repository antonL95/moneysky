<?php

declare(strict_types=1);

namespace App\Data\App\BankAccount;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class BankAccountData extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
