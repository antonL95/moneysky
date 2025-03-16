<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use App\Enums\BankAccountStatus;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class AccountMetadataData extends Data
{
    public function __construct(
        public string $id,
        public string|Optional $iban,
        #[WithCast(EnumCast::class)]
        public BankAccountStatus $status,
        public string $institution_id,
        public string|Optional $owner_name,
    ) {}
}
