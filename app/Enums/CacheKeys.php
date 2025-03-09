<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheKeys: string
{
    case TRANSACTION_AGGREGATE = 'transaction_aggregate:%s:%s';
    case USER_TRANSACTIONS = 'user_transactions:%s:%s:%s';
}
