<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Currency\ConvertCurrency;
use App\Crypto\Clients\CovalenthqClient;
use App\Crypto\Clients\KrakenClient;
use App\Crypto\Contracts\ICryptoClient;
use App\Crypto\Contracts\IExchangeClient;
use App\MarketData\Clients\AlphaVantageClient;
use App\MarketData\Contracts\IStockMarketClient;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            IExchangeClient::class,
            KrakenClient::class,
        );
        $this->app->bind(
            ICryptoClient::class,
            CovalenthqClient::class,
        );
        $this->app->bind(
            IStockMarketClient::class,
            AlphaVantageClient::class,
        );

        $this->app->singleton(ConvertCurrency::class, fn () => new ConvertCurrency);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', static fn (User $user) => $user->canAccessPulse());
    }
}
