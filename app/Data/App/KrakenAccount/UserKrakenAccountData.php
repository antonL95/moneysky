<?php

declare(strict_types=1);

namespace App\Data\App\KrakenAccount;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserKrakenAccountData extends Data
{
    public function __construct(
        public int $id,
        public string $apiKey,
        public string $privateKey,
        public ?string $balance,
    ) {}
}
