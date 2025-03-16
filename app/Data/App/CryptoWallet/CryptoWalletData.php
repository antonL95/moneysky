<?php

declare(strict_types=1);

namespace App\Data\App\CryptoWallet;

use App\Enums\ChainType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class CryptoWalletData extends Data
{
    public function __construct(
        public string $address,
        public ChainType $chainType,
    ) {}
}
