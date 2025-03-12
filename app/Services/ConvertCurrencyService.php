<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CacheKeys;
use App\Helpers\CurrencyHelper;
use App\Http\Integrations\Fixer\FixerConnector;
use App\Http\Integrations\Fixer\Requests\GetLatestCurrencyRates;
use Illuminate\Support\Facades\Cache;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\FixedExchange;
use Money\Money;

final class ConvertCurrencyService
{
    /** @var array<string, numeric-string> */
    private static array $conversionRate;

    public function convertSimple(
        int $amount,
        string $fromCurrency,
        string $toCurrency,
    ): int {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        if ($fromCurrency === '') {
            return $amount;
        }

        if ($toCurrency === '') {
            return $amount;
        }

        return (int) $this->convert(
            new Money($amount, new Currency($fromCurrency)),
            new Currency($toCurrency),
        )->getAmount();
    }

    public function convert(Money $money, Currency $toCurrency): Money
    {
        $key = sprintf('%s-%s', $money->getCurrency()->getCode(), $toCurrency->getCode());

        self::$conversionRate[$key] ??= $this->getExchangeRate(
            $money->getCurrency(),
            $toCurrency,
        );

        $convertor = new Converter(
            new ISOCurrencies,
            new FixedExchange(
                [
                    $money->getCurrency()->getCode() => [
                        $toCurrency->getCode() => self::$conversionRate[$key],
                    ],
                ],
            ),
        );

        return $convertor->convert($money, $toCurrency);
    }

    /**
     * @return numeric-string
     */
    private function getExchangeRate(
        Currency $from,
        Currency $to,
    ): string {
        /** @var array<string, float> $rates */
        $rates = Cache::remember(
            CacheKeys::EXCHANGE_RATES->value,
            now()->addDay(),
            fn (): array => $this->fetchExchangeRates(),
        );

        if ($from->equals($to)) {
            return '1';
        }

        if (! isset($rates[$to->getCode()], $rates[$from->getCode()])) {
            return '0';
        }

        return (string) ($rates[$to->getCode()] / $rates[$from->getCode()]);
    }

    /**
     * @return array<string, float>
     */
    private function fetchExchangeRates(): array
    {
        $connector = new FixerConnector;

        /** @var array<string, float> $result */
        $result = $connector->send(new GetLatestCurrencyRates(
            CurrencyHelper::defaultCurrency(),
            CurrencyHelper::getCurrencies(),
        ))->array('rates');

        return $result;
    }
}
