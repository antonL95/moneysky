<?php

declare(strict_types=1);

use App\Exceptions\StockMarketClientException;
use App\Http\Integrations\AlphaVantage\Requests\TimeSeriesDaily;
use App\Services\StockMarketService;
use Saloon\Http\Faking\MockResponse;

it('can get a latest price for ticker', function () {
    $ticker = 'AAPL';

    $service = app(StockMarketService::class);

    $value = $service->fetchPriceForTicker($ticker);

    expect($value)->toBeInt()->and($value)->toEqual(23907); // This is a fake value from fixture
});

it('has a wrong value cached', function () {
    $ticker = 'AAPL';

    Illuminate\Support\Facades\Cache::put('stock-market-AAPL', 'fakePrice');

    $service = app(StockMarketService::class);

    expect(static fn () => $service->fetchPriceForTicker($ticker))->toThrow(
        StockMarketClientException::class,
        'Invalid price cached',
    );
});

it('has a correct value cached', function () {
    $ticker = 'AAPL';

    Illuminate\Support\Facades\Cache::put('stock-market-AAPL', 1234);

    $service = app(StockMarketService::class);

    expect($service->fetchPriceForTicker($ticker))->toBeInt()->toEqual(1234);
});

it('has a wrong structure', function () {
    $ticker = 'AAPL';

    Saloon\Laravel\Facades\Saloon::fake([
        TimeSeriesDaily::class => MockResponse::make([
            'Time Series (Daily)' => [
                '2021-01-01' => [
                    '1. open' => '1234',
                    '2. high' => '1234',
                    '3. low' => '1234',
                    '5. volume' => '1234',
                ],
            ],
        ]),
    ]);

    $service = app(StockMarketService::class);

    expect(static fn () => $service->fetchPriceForTicker($ticker))->toThrow(
        StockMarketClientException::class,
        'Invalid value',
    );
});

it('has a wrong parent structure', function () {
    $ticker = 'AAPL';

    $data = [
        'Time Series (weekly)' => [
            '2021-01-01' => [
                '1. open' => '1234',
                '2. high' => '1234',
                '3. low' => '1234',
                '4. close' => '1234',
                '5. volume' => '1234',
            ],
        ],
    ];
    Saloon\Laravel\Facades\Saloon::fake([
        TimeSeriesDaily::class => MockResponse::make($data),
    ]);

    $service = app(StockMarketService::class);

    expect(static fn () => $service->fetchPriceForTicker($ticker))->toThrow(
        StockMarketClientException::class,
        sprintf('Invalid response from AlphaVantage API: %s', json_encode($data)),
    );
});

it('has a wrong value type', function () {
    $ticker = 'AAPL';

    $data = [
        'Time Series (Daily)' => [
            '2021-01-01' => [
                '1. open' => '1234',
                '2. high' => '1234',
                '3. low' => '1234',
                '4. close' => 'asdfasdf',
                '5. volume' => '1234',
            ],
        ],
    ];
    Saloon\Laravel\Facades\Saloon::fake([
        TimeSeriesDaily::class => MockResponse::make($data),
    ]);

    $service = app(StockMarketService::class);

    expect(static fn () => $service->fetchPriceForTicker($ticker))->toThrow(
        StockMarketClientException::class,
        'Invalid response from AlphaVantage API: asdfasdf',
    );
});

it('assigns correct currency', function () {
    $ticker = 'AAPL.LON';

    $data = [
        'Time Series (Daily)' => [
            '2021-01-01' => [
                '1. open' => '1234',
                '2. high' => '1234',
                '3. low' => '1234',
                '4. close' => '1234',
                '5. volume' => '1234',
            ],
        ],
    ];
    Saloon\Laravel\Facades\Saloon::fake([
        TimeSeriesDaily::class => MockResponse::make($data),
    ]);

    $service = app(StockMarketService::class);

    $price = $service->fetchPriceForTicker($ticker);

    expect($price)->toBeInt()->toEqual(159200);
});

it('assigns correct currency for eur', function () {
    $ticker = 'AAPL.DEX';

    $data = [
        'Time Series (Daily)' => [
            '2021-01-01' => [
                '1. open' => '1234',
                '2. high' => '1234',
                '3. low' => '1234',
                '4. close' => '1234',
                '5. volume' => '1234',
            ],
        ],
    ];
    Saloon\Laravel\Facades\Saloon::fake([
        TimeSeriesDaily::class => MockResponse::make($data),
    ]);

    $service = app(StockMarketService::class);

    $price = $service->fetchPriceForTicker($ticker);

    expect($price)->toBeInt()->toEqual(133700);
});
