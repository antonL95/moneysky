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
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
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
        VerifyEmail::toMailUsing(static function ($notifiable, $url) {
            return (new MailMessage)
                ->mailer('resend')
                ->subject(Lang::get('Verify Email Address'))
                ->line(Lang::get('Please click the button below to verify your email address.'))
                ->action(Lang::get('Verify Email Address'), $url)
                ->line(Lang::get('If you did not create an account, no further action is required.'));
        });
    }
}
