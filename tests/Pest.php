<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use App\Http\Integrations\AlphaVantage\Requests\TimeSeriesDaily;
use App\Http\Integrations\Fixer\Requests\GetLatestCurrencyRates;
use App\Http\Integrations\GoCardless\Requests\Requisitions\DeleteRequisitionById;
use App\Http\Integrations\GoCardless\Requests\Token\ObtainNewAccessRefreshTokenPair;
use App\Http\Integrations\Kraken\Requests\BalanceRequest;
use App\Http\Integrations\Kraken\Requests\TickerRequest;
use Saloon\Http\Faking\MockResponse;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function () {
        Saloon\Laravel\Facades\Saloon::fake([
            GetLatestCurrencyRates::class => MockResponse::fixture('latest-currency-rates.json'),
            TimeSeriesDaily::class => MockResponse::fixture('ticker-time-series-daily.json'),
            TickerRequest::class => MockResponse::fixture('kraken-ticker-ticker.json'),
            BalanceRequest::class => MockResponse::make([
                'result' => [
                    'XETHZ' => '1',
                ],
            ]),
            DeleteRequisitionById::class => MockResponse::make([
                'result' => [true],
            ]),
            ObtainNewAccessRefreshTokenPair::class => MockResponse::make(
                body: '{"access": "asdfasdf", "access_expires": 123, "refresh": "asdfasdfa", "refresh_expires": 123}',
            ),
        ]);
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
