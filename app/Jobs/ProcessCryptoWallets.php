<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Http\Integrations\Covalent\CovalentConnector;
use App\Http\Integrations\Covalent\Requests\GetTokenBalancesForAddress;
use App\Models\UserCryptoWallets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCryptoWallets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public UserCryptoWallets $wallet,
    ) {
    }

    public function handle(
        CovalentConnector $connector,
    ): void {
        $response = $connector->send(
            new GetTokenBalancesForAddress(
                $this->wallet->chain_type->getChainName(),
                $this->wallet->wallet_address,
            ),
        );

        $items = $response->collect('data.items');

        $totalCents = 0;
        $tokens = [];

        foreach ($items as $currency) {
            if (!\is_array($currency)) {
                continue;
            }

            $quoteCents = (int) round($currency['quote'] * 100);
            $totalCents += $quoteCents;
            $tokens[$currency['quote']] = $quoteCents;
        }

        $this->wallet->balance_cents = $totalCents;
        $this->wallet->tokens = $tokens;

        $this->wallet->save();
    }
}
