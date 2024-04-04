<?php

declare(strict_types=1);

namespace App\Actions\Currency;

use Exchanger\Service\CentralBankOfCzechRepublic;
use Exchanger\Service\EuropeanCentralBank;
use Exchanger\Service\OpenExchangeRates;
use Illuminate\Support\Facades\Cache;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange\FixedExchange;
use Money\Exchange\SwapExchange;
use Money\Money;
use Swap\Swap;

class ConvertCurrency
{
    private SwapExchange $czkExchange;

    private SwapExchange $eurExchange;

    private SwapExchange $usdExchange;

    public function __construct()
    {
        $this->czkExchange = new SwapExchange(
            new Swap(new CentralBankOfCzechRepublic),
        );
        $this->eurExchange = new SwapExchange(
            new Swap(new EuropeanCentralBank),
        );
        $this->usdExchange = new SwapExchange(
            new Swap(
                new OpenExchangeRates(
                    options: [
                        'app_id' => config('services.open_exchange_rates.app_id'),
                    ],
                ),
            ),
        );
    }

    public function convert(Money $money, Currency $toCurrency): Money
    {
        $exchangeRate = $this->getExchangeRate(
            $money->getCurrency(),
            $toCurrency,
        );

        if (!is_numeric($exchangeRate)) {
            $exchangeRate = '1.0';
        }

        $convertor = new Converter(
            new ISOCurrencies,
            new FixedExchange(
                [
                    $money->getCurrency()->getCode() => [
                        $toCurrency->getCode() => $exchangeRate,
                    ],
                ],
            ),
        );

        return $convertor->convert($money, $toCurrency);
    }

    private function getExchangeRate(
        Currency $from,
        Currency $to,
    ): string {
        $key = sprintf('exchangeRate-%s-%s', $from->getCode(), $to->getCode());

        if (Cache::missing($key)) {
            Cache::put($key, $this->getQuote($from, $to), 60 * 60 * 24);
        }

        /** @var CurrencyPair $exchangeRate */
        $exchangeRate = Cache::get($key);

        return $exchangeRate->getConversionRatio();
    }

    private function getQuote(
        Currency $from,
        Currency $to,
    ): CurrencyPair {
        if ($from->getCode() === $to->getCode()) {
            return new CurrencyPair(
                $from,
                $to,
                '1',
            );
        }

        if ($to->getCode() === 'CZK') {
            return $this->czkExchange->quote($from, $to);
        }

        if ($from->getCode() === 'CZK') {
            return new CurrencyPair(
                $from,
                $to,
                (string) (1 / (float) $this->czkExchange->quote($to, $from)->getConversionRatio()),
            );
        }

        if ($from->getCode() === 'EUR') {
            return $this->eurExchange->quote($from, $to);
        }

        if ($from->getCode() === 'USD') {
            return new CurrencyPair(
                $from,
                $to,
                '1',
            );
        }

        $currencyPair = $this->usdExchange->quote(new Currency('USD'), $from);

        if ($to->getCode() === 'USD') {
            return new CurrencyPair(
                $from,
                new Currency('USD'),
                (string) (1 / (float) $currencyPair->getConversionRatio()),
            );
        }

        return $currencyPair;
    }
}
