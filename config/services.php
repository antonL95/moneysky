<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'kraken' => [
        'url' => env('KRAKEN_API_URL'),
    ],

    'aplhavantage' => [
        'url' => env('APLHA_VANTAGE_API_URL'),
        'apiKey' => env('APLHA_VANTAGE_API_KEY'),
    ],

    'covalenthq' => [
        'url' => env('COVALENTHQ_API_URL'),
        'apiKey' => env('COVALENTHQ_API_KEY'),
    ],
    'openexchangerates' => [
        'app_id' => env('OPENEXCHANGERATES_APP_ID'),
    ],

    'bank_data_api' => [
        'url' => env('BANK_DATA_API_URL', ''),
        'secret_key' => env('BANK_DATA_API_SECRET_KEY', ''),
        'secret_id' => env('BANK_DATA_API_SECRET_ID', ''),
    ],
];
