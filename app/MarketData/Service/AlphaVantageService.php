<?php

declare(strict_types=1);

namespace App\MarketData\Service;

use App\Actions\Currency\ConvertCurrency;
use App\Http\Integrations\AlphaVantage\AlphaVantage;
use App\Http\Integrations\AlphaVantage\Requests\TimeSeriesDaily;
use App\MarketData\Exceptions\AlphaVantageClientException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Money\Currency;
use Money\Money;

use function Safe\json_encode;

final readonly class AlphaVantageService
{
    private const array EXCHANGE_CURRENCY_MAP = [
        'LON' => 'GBP',
        'L' => 'GBP',
        'TRT' => 'CAD',
        'TRV' => 'CAD',
        'DEX' => 'EUR',
        'BSE' => 'INR',
        'SHH' => 'CNY',
        'SHZ' => 'CNY',
    ];

    public function __construct(
        private AlphaVantage $connector,
    ) {
    }

    /**
     * @throws AlphaVantageClientException
     */
    public function fetchPriceForTicker(
        string $ticker,
    ): int {
        $cacheKey = sprintf('stock-market-%s', $ticker);

        if (!Cache::missing($cacheKey)) {
            $price = Cache::get($cacheKey);

            if (!is_numeric($price)) {
                throw AlphaVantageClientException::invalidPriceCached();
            }

            return (int) $price;
        }

        $response = $this->connector->debug()->send(
            new TimeSeriesDaily($ticker),
        );

        $data = (array) Arr::last($response->json());

        $timeSeries = array_values($data);
        $latestTimeSeries = array_values((array) $timeSeries[0]);
        $closeValue = $latestTimeSeries[3];

        if (!is_numeric($closeValue)) {
            throw AlphaVantageClientException::invalidResponseFromApi(
                json_encode($closeValue),
            );
        }

        $price = $this->convertPriceToUsdCents($ticker, (float) $closeValue);

        Cache::put($cacheKey, $price, 60 * 60 * 24);

        return $price;
    }

    private function convertPriceToUsdCents(
        string $ticker,
        float $price,
    ): int {
        $exchanges = explode('.', $ticker);

        if (isset($exchanges[1])) {
            $exchange = $exchanges[1];
            $currency = self::EXCHANGE_CURRENCY_MAP[$exchange];
        } else {
            $exchange = 'NYSE';
            $currency = 'USD';
        }

        if ($exchange === 'NYSE') {
            return (int) ($price * 100);
        }

        $tickerCurrency = new Currency($currency);
        $usdCurrency = new Currency('USD');
        $convertor = new ConvertCurrency;

        if ($exchange === 'LON' || $exchange === 'L') {
            return (int) $convertor->convert(
                new Money((int) $price, $tickerCurrency),
                $usdCurrency,
            )->getAmount();
        }

        return (int) $convertor->convert(
            new Money((int) $price, $tickerCurrency),
            $usdCurrency,
        )->getAmount() * 100;
    }
}
