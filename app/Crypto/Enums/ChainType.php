<?php

declare(strict_types=1);

namespace App\Crypto\Enums;

enum ChainType: string
{
    case ETH = 'eth';
    case MATIC = 'matic';
    case BTC = 'btc';

    public function getChainName(): string
    {
        return match ($this) {
            self::ETH => 'eth-mainnet',
            self::MATIC => 'matic-mainnet',
            self::BTC => 'btc-mainnet',
        };
    }
}
