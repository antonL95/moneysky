<?php

declare(strict_types=1);

namespace App\Enums;

enum ChainType: string
{
    case ETH = 'eth';
    case MATIC = 'matic';
    case BTC = 'btc';

    public function getPrettyName(): string
    {
        return match ($this) {
            self::ETH => 'Ethereum',
            self::MATIC => 'Polygon (matic)',
            self::BTC => 'Bitcoin',
        };
    }
}
