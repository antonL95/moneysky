<?php

declare(strict_types=1);

namespace App\Enums;

enum Subscriptions: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
