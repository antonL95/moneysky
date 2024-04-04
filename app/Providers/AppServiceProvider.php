<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Currency\ConvertCurrency;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use PostHog\PostHog;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConvertCurrency::class, fn () => new ConvertCurrency);
    }

    public function boot(): void
    {
        Gate::define('viewPulse', static fn (User $user) => $user->canAccessPulse());
        $postHogApiKey = config('services.post_hog.api_key');

        if (\is_string($postHogApiKey)) {
            PostHog::init(
                $postHogApiKey,
                [
                    'host' => 'https://eu.posthog.com',
                ],
            );
        }
    }
}
