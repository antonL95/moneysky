<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheKeys: string
{
    case BANK_DATA_API_ACCESS_TOKEN = 'bank_data_api_access_token';
    case BANK_DATA_API_REFRESH_TOKEN = 'bank_data_api_refresh_token';
}
