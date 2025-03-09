<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Money\Currencies\ISOCurrencies;

final class CurrencyHelper
{
    /** @var string[] */
    private static array $currencies = [];

    /**
     * @return string[]
     */
    public static function getCurrencies(): array
    {
        if (self::$currencies === []) {
            $currencies = [];

            foreach ((new ISOCurrencies)->getIterator() as $currency) {
                $currencyCode = $currency->getCode();
                $currencies[$currencyCode] = $currencyCode;
            }

            self::$currencies = $currencies;
        }

        return self::$currencies;
    }

    /**
     * @return non-empty-string
     */
    public static function defaultCurrency(): string
    {
        $currency = Config::string('app.default_currency', 'EUR');

        return $currency === ''
            ? 'EUR'
            : $currency;
    }
}
