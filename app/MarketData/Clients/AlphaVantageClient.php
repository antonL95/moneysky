<?php

declare(strict_types=1);

namespace App\MarketData\Clients;

use App\Actions\Currency\ConvertCurrency;
use App\MarketData\Contracts\IStockMarketClient;
use App\MarketData\Exceptions\AlphaVantageClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Money\Currency;
use Money\Money;

use function Safe\json_encode;

class AlphaVantageClient implements IStockMarketClient
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

    private string $apiKey;

    /**
     * @throws AlphaVantageClientException
     */
    public function __construct(
        private readonly string $apiUrl = 'https://www.alphavantage.co/query',
    ) {
        $apiKey = Config::get('services.aplhavantage.apiKey');

        if (!\is_string($apiKey)) {
            throw AlphaVantageClientException::invalidConfig();
        }

        $this->apiKey = $apiKey;
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

        $data = (array) Http::get($this->apiUrl, [
            'symbol' => $ticker,
            'function' => 'TIME_SERIES_DAILY',
            'apikey' => $this->apiKey,
        ])->json();

        if (!isset($data['Time Series (Daily)'])) {
            throw AlphaVantageClientException::invalidResponseFromApi(
                json_encode($data),
            );
        }

        $timeSeries = (array) $data['Time Series (Daily)'];

        $firstKey = array_key_first($timeSeries);

        if (!\is_string($firstKey)) {
            throw AlphaVantageClientException::invalidResponseFromApi(
                json_encode($data),
            );
        }

        $latestTimeSeries = (array) $timeSeries[$firstKey];

        if (!isset($latestTimeSeries['4. close'])) {
            throw AlphaVantageClientException::invalidResponseFromApi(
                json_encode($data),
            );
        }

        $closeValue = $latestTimeSeries['4. close'];

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
