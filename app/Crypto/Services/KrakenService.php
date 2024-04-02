<?php

declare(strict_types=1);

namespace App\Crypto\Services;

use App\Crypto\Clients\KrakenClient;
use App\Crypto\Models\KrakenTradingPairs;
use App\Crypto\Models\UserKrakenAccount;

readonly class KrakenService
{
    public function __construct(
        protected KrakenClient $krakenClient,
    ) {
    }

    public function saveTickerPairs(): int
    {
        $data = $this->krakenClient->fetchTickerPairsAndTradingValues();

        $temp = [];

        foreach ($data as $pair) {
            $temp[] = [
                'key_pair' => $pair->pair,
                'crypto' => $pair->crypto,
                'fiat' => $pair->fiat,
                'trade_value_cents' => $pair->tradeValue,
            ];
        }

        KrakenTradingPairs::upsert(
            $temp,
            ['key_pair'],
            ['trade_value_cents'],
        );

        return $data->count();
    }

    public function saveBalances(
        UserKrakenAccount $krakenAccount,
    ): void {
        $accountBalances = $this->krakenClient->fetchAccountBalance(
            $krakenAccount->api_key,
            $krakenAccount->private_key,
        );

        $krakenAccount->update([
            'balance_cents' => $accountBalances,
        ]);
    }
}
