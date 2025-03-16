<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;
use Saloon\Http\Senders\GuzzleSender;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->singleton(GuzzleSender::class, fn (): GuzzleSender => new GuzzleSender);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::shouldBeStrict(! $this->app->isProduction());
        Model::unguard();

        Gate::define('viewPulse', static fn (User $user): bool => $user->canAccessPulse());
        Date::use(CarbonImmutable::class);
        DB::prohibitDestructiveCommands($this->app->isProduction());
        Password::defaults(fn () => $this->app->isProduction() ? Password::min(8)->uncompromised() : null);
        URL::forceScheme('https');
    }
}
