<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use App\Enums\TransactionType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserTransactionData extends Data
{
    public function __construct(
        public int $id,
        public string $balance,
        public int|float $amount,
        public ?string $description,
        public string $currency,
        public string $bookedAt,
        public ?int $userManualEntryId,
        public ?int $transactionTagId,
        public TransactionType $transactionType,
        public ?string $bankAccountName,
        public ?string $cashWalletName,
        public bool $hidden = false,
    ) {}
}
