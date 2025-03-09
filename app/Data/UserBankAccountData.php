<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\BankAccountStatus;
use Spatie\LaravelData\Data;

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
