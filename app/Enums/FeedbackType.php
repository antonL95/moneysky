<?php

declare(strict_types=1);

namespace App\Enums;

enum FeedbackType: string
{
    case BUG = 'bug';
    case IMPROVEMENT = 'improvement';
    case FEATURE = 'feature';
    case CRITICAL_ERROR = 'critical_error';

    public function label(): string
    {
        return match ($this) {
            self::BUG => 'Bug',
            self::IMPROVEMENT => 'Improvement',
            self::FEATURE => 'Feature',
            self::CRITICAL_ERROR => 'Critical Error',
        };
    }
}
