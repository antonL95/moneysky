<?php

declare(strict_types=1);

namespace App\Data\App\BankAccount;

use App\Enums\BankAccountStatus;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserBankAccountData extends Data
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $balance,
        public bool $accessExpired,
        public BankAccountStatus $status,
    ) {}
}
