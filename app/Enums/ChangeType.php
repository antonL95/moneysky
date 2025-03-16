<?php

declare(strict_types=1);

namespace App\Enums;

enum ChangeType: string
{
    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';
}
