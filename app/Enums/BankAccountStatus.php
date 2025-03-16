<?php

declare(strict_types=1);

namespace App\Enums;

enum BankAccountStatus: string
{
    case READY = 'READY';
    case DISCOVERED = 'DISCOVERED';
    case ERROR = 'ERROR';
    case EXPIRED = 'EXPIRED';
    case PROCESSING = 'PROCESSING';
    case SUSPENDED = 'SUSPENDED';

    public function getName(): string
    {
        return match ($this) {
            self::READY => __('Ready'),
            self::DISCOVERED => __('Discovered'),
            self::ERROR => __('Error'),
            self::EXPIRED => __('Expired'),
            self::PROCESSING => __('Processing'),
            self::SUSPENDED => __('Suspended'),
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::READY => 'green',
            default => 'red',
        };
    }
}
