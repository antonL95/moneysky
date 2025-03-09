<?php

declare(strict_types=1);

namespace App\Enums;

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

    public function getPrettyName(): string
    {
        return match ($this) {
            self::ETH => 'Ethereum',
            self::MATIC => 'Polygon (matic)',
            self::BTC => 'Bitcoin',
        };
    }
}
