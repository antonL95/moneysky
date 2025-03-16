<?php

declare(strict_types=1);

return [
    'name' => 'Moneysky',
    'manifest' => [
        'name' => env('APP_NAME', 'Moneysky'),
        'short_name' => 'PWA',
        'start_url' => '/dashboard',
        'background_color' => '#000000',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '48x48' => [
                'path' => '/images/icons/icon-48.png',
                'purpose' => 'any',
            ],
            '72x72' => [
                'path' => '/images/icons/icon-72.png',
                'purpose' => 'any',
            ],
            '96x96' => [
                'path' => '/images/icons/icon-96.png',
                'purpose' => 'any',
            ],
            '144x144' => [
                'path' => '/images/icons/icon-144.png',
                'purpose' => 'any',
            ],
            '192x192' => [
                'path' => '/images/icons/icon-192.png',
                'purpose' => 'any',
            ],
            '512x512' => [
                'path' => '/images/icons/icon-512.png',
                'purpose' => 'any',
            ],
        ],
        'splash' => [
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1920x1080' => '/images/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Dashboard',
                'description' => 'Navigate to dashboard',
                'url' => '/dashboard?tab=investments',
                'icons' => [
                    'src' => '/images/icons/icon-72x72.png',
                    'purpose' => 'any',
                ],
            ],
            [
                'name' => 'Budgets',
                'description' => 'Navigate to budgets',
                'url' => '/dashboard?tab=budgets',
            ],
        ],
        'custom' => [],
    ],
];
