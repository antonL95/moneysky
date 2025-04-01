<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ChainType;
use App\Http\Integrations\Blockchain\BlockchainConnector;
use App\Http\Integrations\Blockchain\Requests\GetTicker;
use App\Http\Integrations\Blockchain\Requests\GetTokenBalancesForAddress as GetBitcoinBalancesForAddress;
use App\Http\Integrations\Moralis\MoralisConnector;
use App\Http\Integrations\Moralis\Requests\GetTokenBalancesForAddress;
use App\Models\UserCryptoWallet;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Saloon\Http\Response;

use function is_array;

final class ProcessCryptoWalletsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public UserCryptoWallet $wallet,
    ) {}

    public function handle(
        MoralisConnector $evmConnector,
        BlockchainConnector $btcConnector,
    ): void {
        $user = $this->wallet->user;

        if ($user === null) {
            return;
        }

        $response = match ($this->wallet->chain_type) {
            ChainType::ETH => $evmConnector->send(
                new GetTokenBalancesForAddress(
                    $this->wallet->chain_type->value,
                    $this->wallet->wallet_address,
                ),
            ),
            ChainType::BTC => $btcConnector->send(
                new GetBitcoinBalancesForAddress(
                    $this->wallet->wallet_address,
                ),
            ),
            default => null,
        };

        if (! $response instanceof Response) {
            return;
        }

        $usdBtcValueResponse = [];
        $items = [];

        if ($this->wallet->chain_type === ChainType::ETH) {
            $items = (array) $response->array()['result'];
        } elseif ($this->wallet->chain_type === ChainType::BTC) {
            $items = $response->array();
            $usdBtcValueResponse = (array) $btcConnector->send(new GetTicker)->array();
        }

        $totalCents = 0;
        $tokens = [];

        foreach ($items as $currency) {
            if (! is_array($currency)) {
                continue;
            }

            if ($this->wallet->chain_type === ChainType::ETH) {
                if (! isset($currency['usd_value'])) {
                    continue;
                }

                if (! is_numeric($currency['usd_value'])) {
                    continue;
                }

                $quoteCents = (int) round(((float) $currency['usd_value']) * 100);
                $totalCents += $quoteCents;
                $tokens[$currency['symbol']] = $quoteCents;

                continue;
            }

            if ($this->wallet->chain_type === ChainType::BTC) {
                if (! isset($currency['final_balance'])) {
                    continue;
                }

                if (! is_array($usdBtcValueResponse['USD'])) {
                    continue;
                }
                if (! is_numeric($usdBtcValueResponse['USD']['last'])) {
                    continue;
                }
                if (! is_numeric($currency['final_balance'])) {
                    continue;
                }

                $baseBalance = (float) ($currency['final_balance'] / 100_000_000);
                $usdCents = (int) ((float) $usdBtcValueResponse['USD']['last'] * 100);

                $quoteCents = (int) round($baseBalance * $usdCents);
                $totalCents += $quoteCents;
                $tokens['BTC'] = $quoteCents;
            }
        }

        $this->wallet->balance_cents = $totalCents;
        $this->wallet->tokens = $tokens;

        $this->wallet->save();

        ProcessSnapshotJob::dispatch($user);
    }
}
