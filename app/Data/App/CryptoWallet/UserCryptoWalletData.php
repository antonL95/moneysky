<?php

declare(strict_types=1);

namespace App\Data\App\CryptoWallet;

use App\Enums\ChainType;
use Spatie\LaravelData\Data;

final class UserCryptoWalletData extends Data
{
    public function __construct(
        public int $id,
        public string $walletAddress,
        public ChainType $chainType,
        public string $chainName,
        public ?string $balance,
    ) {}
}
