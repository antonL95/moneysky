<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\StockMarketClientException;
use App\Http\Integrations\AlphaVantage\AlphaVantage;
use App\Http\Integrations\AlphaVantage\Requests\TimeSeriesDaily;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Money\Currency;
use Money\Money;

final readonly class StockMarketService
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
    ) {}

    /**
     * @throws StockMarketClientException
     */
    public function fetchPriceForTicker(
        string $ticker,
    ): int {
        $cacheKey = sprintf('stock-market-%s', $ticker);

        if (! Cache::missing($cacheKey)) {
            $price = Cache::get($cacheKey);

            if (! is_numeric($price)) {
                Cache::forget($cacheKey);

                throw StockMarketClientException::invalidPriceCached();
            }

            return (int) $price;
        }

        /** @var Collection<string, array<string, array<string, float|int|string>>> $response */
        $response = $this->connector->send(
            new TimeSeriesDaily($ticker),
        )->collect();

        if (! $response->has('Time Series (Daily)')) {
            throw StockMarketClientException::invalidResponseFromApi(
                $response->toJson(),
            );
        }

        $data = (array) Arr::first((array) $response->get('Time Series (Daily)'));

        if (! array_key_exists('4. close', $data)) {
            throw StockMarketClientException::invalidValue();
        }

        $closeValue = $data['4. close'];

        if (! is_numeric($closeValue)) {
            throw StockMarketClientException::invalidResponseFromApi(
                $closeValue,
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
        $convertor = new ConvertCurrencyService;

        return (int) $convertor->convert(
            new Money((int) $price, $tickerCurrency),
            $usdCurrency,
        )->getAmount() * 100;
    }
}
