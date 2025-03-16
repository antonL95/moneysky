<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class PrivacyController
{
    public function __invoke(): Response
    {
        return Inertia::render('static-page/index', [
            'content' => Str::markdown(
                (string) file_get_contents(
                    resource_path('markdown/policy.md'),
                ),
                [
                    'external_link' => [
                        'open_in_new_window' => true,
                    ],
                ],
            ),
            'title' => 'Privacy Policy',
        ]);
    }
}
