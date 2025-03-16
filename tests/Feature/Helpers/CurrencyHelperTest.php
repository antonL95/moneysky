<?php

declare(strict_types=1);

use App\Helpers\CurrencyHelper;
use Illuminate\Support\Facades\Config;

it('returns default value', function () {
    Config::set('app.default_currency', '');
    $currency = CurrencyHelper::defaultCurrency();
    expect($currency)->toBe('EUR');
});

it('returns default currency from env', function () {
    $currency = CurrencyHelper::defaultCurrency();
    expect($currency)->toBe('USD');
});

it('returns list of currencies', function () {
    $currencies = CurrencyHelper::getCurrencies();
    expect($currencies)->toBeArray()->and($currencies)->toHaveKeys(['EUR', 'CZK']);
});
