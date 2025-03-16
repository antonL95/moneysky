<?php

declare(strict_types=1);

namespace App\Enums;

enum AssetType: string
{
    case BANK_ACCOUNTS = 'bank-accounts';
    case STOCK_MARKET = 'stock-market';
    case CRYPTO = 'crypto';
    case EXCHANGE = 'exchange';
    case MANUAL_ENTRIES = 'manual-entries';

    public function label(): string
    {
        return match ($this) {
            self::CRYPTO => 'Digital Assets',
            self::STOCK_MARKET => 'Stock Market',
            self::EXCHANGE => 'Exchange',
            self::BANK_ACCOUNTS => 'Bank Accounts',
            self::MANUAL_ENTRIES => 'Manual Entries',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CRYPTO => 'rgba(46, 184, 138, 0.5)',
            self::STOCK_MARKET => 'rgba(226, 54, 112, 0.5)',
            self::EXCHANGE => 'rgba(232, 140, 48, 0.5)',
            self::BANK_ACCOUNTS => 'rgba(38, 98, 217, 0.5)',
            self::MANUAL_ENTRIES => 'rgba(175, 87, 219, 0.5)',
        };
    }

    public function colorFull(): string
    {
        return match ($this) {
            self::CRYPTO => 'rgba(46, 184, 138, 1)',
            self::STOCK_MARKET => 'rgba(226, 54, 112, 1)',
            self::EXCHANGE => 'rgba(232, 140, 48, 1)',
            self::BANK_ACCOUNTS => 'rgba(38, 98, 217, 1)',
            self::MANUAL_ENTRIES => 'rgba(175, 87, 219, 1)',
        };
    }
}
