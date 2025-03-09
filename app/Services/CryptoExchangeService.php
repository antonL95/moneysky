<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\KrakenParsedPairData;
use App\Http\Integrations\Kraken\KrakenConnector;
use App\Http\Integrations\Kraken\Requests\BalanceRequest;
use App\Http\Integrations\Kraken\Requests\TickerRequest;
use App\Models\KrakenTradingPairs;
use App\Models\UserKrakenAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use function count;
use function in_array;
use function is_array;

final readonly class CryptoExchangeService
{
    private const array ACCEPTED_FIAT = ['USD', 'EUR'];

    public function __construct(
        private KrakenConnector $krakenConnector,
    ) {}

    public function saveTickerPairs(): int
    {
        $response = $this->krakenConnector->send(
            new TickerRequest,
        )->collect('result');

        $temp = [];

        foreach ($response as $pair => $prices) {
            if (! is_array($prices)) {
                continue;
            }
            if (! is_array($prices['c'])) {
                continue;
            }

            if (! isset($prices['c'][0])) {
                continue;
            }
            if (! is_numeric($prices['c'][0])) {
                continue;
            }

            $parsedPair = $this->parseKeyPair($pair);

            if (! in_array($parsedPair->fiat, self::ACCEPTED_FIAT)) {
                continue;
            }

            $closingPrice = (float) $prices['c'][0];
            $tradeValue = (int) floor($closingPrice * 100);

            $temp[] = [
                'key_pair' => $pair,
                'crypto' => $parsedPair->crypto,
                'fiat' => $parsedPair->fiat,
                'trade_value_cents' => $tradeValue,
            ];
        }

        KrakenTradingPairs::upsert(
            $temp,
            ['key_pair'],
            ['trade_value_cents'],
        );

        return count($temp);
    }

    public function saveBalances(
        UserKrakenAccount $krakenAccount,
    ): void {
        $response = $this->krakenConnector->send(
            new BalanceRequest(
                $krakenAccount->api_key,
                $krakenAccount->private_key,
            ),
        )->collect('result');

        $accountBalance = 0;
        /**
         * @var string $ticker
         * @var string $amount
         */
        foreach ($response as $ticker => $amount) {
            $amount = round((float) $amount, 6);
            $ticker = Str::upper($ticker);

            if ($amount <= 0) {
                continue;
            }

            $krakenTradingPairs = KrakenTradingPairs::where('fiat', '=', 'USD')
                ->where(function (Builder $builder) use ($ticker): void {
                    $builder
                        ->where('crypto', $ticker)
                        ->orWhere('crypto', Str::replace('X', '', $ticker))
                        ->orWhere('crypto', 'LIKE', "%$ticker%");
                })->first();

            if ($krakenTradingPairs === null) {
                continue;
            }

            $accountBalance += (int) floor($amount * $krakenTradingPairs->trade_value_cents);
        }

        $krakenAccount->update([
            'balance_cents' => $accountBalance,
        ]);
    }

    private function parseKeyPair(string $pair): KrakenParsedPairData
    {
        $fiatTicker = Str::lower(Str::substr($pair, -3, 3));
        $crypto = Str::replace($fiatTicker, '', Str::lower($pair));

        return new KrakenParsedPairData(
            crypto: Str::upper($crypto),
            fiat: Str::upper($fiatTicker),
        );
    }
}
