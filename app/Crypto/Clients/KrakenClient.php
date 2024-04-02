<?php

declare(strict_types=1);

namespace App\Crypto\Clients;

use App\Crypto\DataTransferObjects\KrakenParsedPairDto;
use App\Crypto\DataTransferObjects\KrakenTickerPairDto;
use App\Crypto\Exceptions\KrakenClientExceptions;
use App\Crypto\Models\KrakenTradingPairs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use function Safe\json_encode;

readonly class KrakenClient
{
    private const array ACCEPTED_FIAT = ['USD', 'EUR'];

    public function __construct(
        protected string $apiUrl = 'https://api.kraken.com',
        protected string $publicSuffix = '/0/public/',
        protected string $privateSuffix = '/0/private/',
    ) {
    }

    /**
     * @return Collection<int, KrakenTickerPairDto>
     *
     * @throws KrakenClientExceptions
     */
    public function fetchTickerPairsAndTradingValues(): Collection
    {
        $data = (array) Http::get($this->apiUrl.$this->publicSuffix.'Ticker')->json();

        if ($data['error'] !== []) {
            throw KrakenClientExceptions::errorResponse(json_encode((array) $data['error']));
        }

        $result = $data['result'];
        $temp = [];

        foreach ($result as $pair => $tradeValues) {
            $parsedPair = self::parseKeyPair($pair);
            if (!\in_array($parsedPair->fiat, self::ACCEPTED_FIAT)) {
                continue;
            }

            $tradeValue = (int) floor($tradeValues['c'][0] * 100);

            $temp[] = new KrakenTickerPairDto(
                Str::upper($pair),
                $parsedPair->crypto,
                $parsedPair->fiat,
                $tradeValue,
            );
        }

        return collect($temp);
    }

    /**
     * @throws KrakenClientExceptions
     */
    public function fetchAccountBalance(
        string $apiKey,
        string $privateKey,
    ): int {
        $nonce = (int) now()->timestamp;
        $request = [
            'nonce' => $nonce,
        ];

        $signedMessage = self::signMessage($privateKey, $this->privateSuffix.'Balance', $request, $nonce);

        $data = (array) Http::send(
            'POST',
            $this->apiUrl.$this->privateSuffix.'Balance',
            [
                'headers' => [
                    'API-Key' => $apiKey,
                    'API-Sign' => $signedMessage,
                ],
                'form_params' => $request,
            ],
        )->json();

        if ($data['error'] !== []) {
            throw KrakenClientExceptions::errorResponse(json_encode((array) $data['error']));
        }

        $result = (array) $data['result'];

        $accountBalance = 0;
        foreach ($result as $ticker => $amount) {
            $amount = round((float) $amount, 6);
            $ticker = Str::upper($ticker);
            if ($amount <= 0) {
                continue;
            }

            $krakenTradingPairs = KrakenTradingPairs::whereCrypto($ticker)->whereFiat('usd')->first();

            if ($krakenTradingPairs === null) {
                $parsedTicker = Str::replace('X', '', $ticker);
                $krakenTradingPairs = KrakenTradingPairs::whereCrypto($parsedTicker)->whereFiat('usd')->first();
            }

            if ($krakenTradingPairs === null) {
                $krakenTradingPairs = KrakenTradingPairs::where(
                    'key_pair',
                    'LIKE',
                    sprintf('%%%s%%', $ticker),
                )->whereFiat(
                    'usd',
                )->first();
            }

            if ($krakenTradingPairs === null) {
                continue;
            }

            $accountBalance += (int) floor($amount * $krakenTradingPairs->trade_value_cents);
        }

        return $accountBalance;
    }

    private static function parseKeyPair(string $pair): KrakenParsedPairDto
    {
        $fiatTicker = Str::lower(Str::substr($pair, -3, 3));
        $crypto = Str::replace($fiatTicker, '', Str::lower($pair));

        return new KrakenParsedPairDto(
            crypto: Str::upper($crypto),
            fiat: Str::upper($fiatTicker),
        );
    }

    /**
     * @param array<string, mixed> $request
     */
    private static function signMessage(string $privateKey, string $path, array $request, int $nonce): string
    {
        $message = http_build_query($request);
        $secret_buffer = base64_decode($privateKey);
        $hash = hash_init('sha256');
        hash_update($hash, $nonce.$message);
        $hash_digest = hash_final($hash, true);
        $hmac = hash_hmac('sha512', $path.$hash_digest, $secret_buffer, true);

        return base64_encode($hmac);
    }
}
