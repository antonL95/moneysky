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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'alpha_vantage' => [
        'api_key' => env('ALPHA_VANTAGE_API_KEY', ''),
    ],

    'covalent' => [
        'api_key' => env('COVALENT_API_KEY', ''),
    ],

    'open_exchange_rates' => [
        'app_id' => env('OPEN_EXCHANGE_RATES_APP_ID', ''),
    ],

    'gocardless' => [
        'secret_key' => env('GOCARDLESS_SECRET_KEY', ''),
        'secret_id' => env('GOCARDLESS_SECRET_ID', ''),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
    ],

    'stripe' => [
        'monthly_price_id' => env('MONTHLY_PRICE_ID', ''),
    ],

    'post_hog' => [
        'api_key' => env('POST_HOG_API_KEY', 'poh'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
    ],

    'moralis' => [
        'api_key' => env('MORALIS_API_KEY'),
    ],

    'fixer' => [
        'api_key' => env('FIXER_API_KEY'),
    ],

];
