<?php

declare(strict_types=1);

use App\Console\Commands\KrakenAssetsCommand;
use App\Http\Integrations\Kraken\Requests\BalanceRequest;
use App\Http\Integrations\Kraken\Requests\TickerRequest;
use App\Models\UserKrakenAccount;
use App\Services\CryptoExchangeService;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    $this->service = app(CryptoExchangeService::class);
});

it('saves ticker pairs', function () {
    $count = $this->service->saveTickerPairs();

    assertDatabaseCount('kraken_trading_pairs', 821);
    expect($count)->toBe(821);
});

it('skips ticker that is not array', function () {
    Saloon::fake([
        TickerRequest::class => MockResponse::make([
            'result' => [
                'XXBTZUSD' => 'not an array',
                'ETHUSD' => [
                    'c' => [
                        '123.45',
                    ],
                ],
            ],
        ]),
    ]);

    $count = $this->service->saveTickerPairs();

    assertDatabaseCount('kraken_trading_pairs', 1);
    expect($count)->toBe(1);
});

it('skips value that is not array', function () {
    Saloon::fake([
        TickerRequest::class => MockResponse::make([
            'result' => [
                'XXBTZUSD' => [
                    'c' => 123,
                ],
                'ETHUSD' => [
                    'c' => [
                        '123.45',
                    ],
                ],
            ],
        ]),
    ]);

    $count = $this->service->saveTickerPairs();

    assertDatabaseCount('kraken_trading_pairs', 1);
    expect($count)->toBe(1);
});

it('skips value that is array but not set value', function () {
    Saloon::fake([
        TickerRequest::class => MockResponse::make([
            'result' => [
                'XXBTZUSD' => [
                    'c' => [],
                ],
                'ETHUSD' => [
                    'c' => [
                        '123.45',
                    ],
                ],
            ],
        ]),
    ]);

    $count = $this->service->saveTickerPairs();

    assertDatabaseCount('kraken_trading_pairs', 1);
    expect($count)->toBe(1);
});

it('skips value that is not numeric', function () {
    Saloon::fake([
        TickerRequest::class => MockResponse::make([
            'result' => [
                'XXBTZUSD' => [
                    'c' => [
                        'asdfasdf',
                    ],
                ],
                'ETHUSD' => [
                    'c' => [
                        '123.45',
                    ],
                ],
            ],
        ]),
    ]);

    $count = $this->service->saveTickerPairs();

    assertDatabaseCount('kraken_trading_pairs', 1);
    expect($count)->toBe(1);
});

it('skips not accepted pair', function () {
    Saloon::fake([
        TickerRequest::class => MockResponse::make([
            'result' => [
                'XXBTZCZK' => [
                    'c' => [
                        '123.45',
                    ],
                ],
                'ETHUSD' => [
                    'c' => [
                        '123.45',
                    ],
                ],
            ],
        ]),
    ]);

    $count = $this->service->saveTickerPairs();

    assertDatabaseCount('kraken_trading_pairs', 1);
    expect($count)->toBe(1);
});

it('saves balances', function () {
    artisan(KrakenAssetsCommand::class);

    $krakenAccount = UserKrakenAccount::factory()->create();

    $this->service->saveBalances($krakenAccount);

    // 2178.15000

    expect($krakenAccount->fresh()->balance_cents)->toBe(217815);
});

it('skips negative balances', function () {
    artisan(KrakenAssetsCommand::class);
    Saloon::fake([
        BalanceRequest::class => MockResponse::make([
            'result' => [
                'XETHZ' => -1,
            ],
        ]),
    ]);

    $krakenAccount = UserKrakenAccount::factory()->create();
    $this->service->saveBalances($krakenAccount);
    expect($krakenAccount->fresh()->balance_cents)->toBe(0);
});

it('skips unknown tickers', function () {
    artisan(KrakenAssetsCommand::class);
    Saloon::fake([
        BalanceRequest::class => MockResponse::make([
            'result' => [
                'asdfasdf' => 100,
            ],
        ]),
    ]);

    $krakenAccount = UserKrakenAccount::factory()->create();
    $this->service->saveBalances($krakenAccount);
    expect($krakenAccount->fresh()->balance_cents)->toBe(0);
});
