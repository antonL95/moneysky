<?php

declare(strict_types=1);

use App\Http\Integrations\Fixer\Requests\GetLatestCurrencyRates;
use App\Services\ConvertCurrencyService;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockResponse;

it('simply coverts values', function () {
    $fromCurrency = 'EUR';
    $toCurrency = 'USD';

    $amount = 100;

    $convertedAmount = app(ConvertCurrencyService::class)
        ->convertSimple($amount, $fromCurrency, $toCurrency);

    expect($convertedAmount)->toBeInt()
        ->and($convertedAmount)
        ->toBe(108);
});

test('simple convert returns value in case of empty "from" value', function () {
    $fromCurrency = '';
    $toCurrency = 'USD';

    $amount = 100;

    $convertedAmount = app(ConvertCurrencyService::class)
        ->convertSimple($amount, $fromCurrency, $toCurrency);

    expect($convertedAmount)->toBeInt()
        ->and($convertedAmount)
        ->toBe(100);
});

test('simple convert returns value in case of empty "to" value', function () {
    $fromCurrency = 'USD';
    $toCurrency = '';

    $amount = 100;

    $convertedAmount = app(ConvertCurrencyService::class)
        ->convertSimple($amount, $fromCurrency, $toCurrency);

    expect($convertedAmount)->toBeInt()
        ->and($convertedAmount)
        ->toBe(100);
});

test('simple convert returns value in case of equal currencies', function () {
    $fromCurrency = 'USD';
    $toCurrency = 'USD';

    $amount = 100;

    $convertedAmount = app(ConvertCurrencyService::class)
        ->convertSimple($amount, $fromCurrency, $toCurrency);

    expect($convertedAmount)->toBeInt()
        ->and($convertedAmount)
        ->toBe(100);
});

it('returns zero value when incorrect response', function () {
    $fromCurrency = 'EUR';
    $toCurrency = 'GBP';

    $amount = 100;
    Saloon::fake([
        GetLatestCurrencyRates::class => MockResponse::make([
            'rates' => [
                'CZK' => 1.0,
            ],
        ]),
    ]);

    Cache::flush();
    $convertedAmount = app(ConvertCurrencyService::class)
        ->convertSimple($amount, $fromCurrency, $toCurrency);

    expect($convertedAmount)->toBeInt()
        ->and($convertedAmount)
        ->toBe(0);
});
