<?php

declare(strict_types=1);

namespace App\Bank\Enums;

enum Status: string
{
    case CREATED = 'CREATED';
    case GIVING_CONSENT = 'GIVING_CONSENT';
    case UNDERGOING_AUTHENTICATION = 'UNDERGOING_AUTHENTICATION';
    case REJECTED = 'REJECTED';
    case SELECTING_ACCOUNTS = 'SELECTING_ACCOUNTS';
    case GRANTING_ACCESS = 'GRANTING_ACCESS';
    case LINKED = 'LINKED';
    case EXPIRED = 'EXPIRED';

    public static function getByShortCode(string $shortCode): self
    {
        return match ($shortCode) {
            'CR' => self::CREATED,
            'GC' => self::GIVING_CONSENT,
            'UA' => self::UNDERGOING_AUTHENTICATION,
            'RJ' => self::REJECTED,
            'SA' => self::SELECTING_ACCOUNTS,
            'GA' => self::GRANTING_ACCESS,
            'LN' => self::LINKED,
            'EX' => self::EXPIRED,
            default => throw new \InvalidArgumentException('Invalid status'),
        };
    }
}
