<?php

declare(strict_types=1);

namespace App\Crypto\Jobs;

use App\Crypto\Clients\CovalenthqClient;
use App\Crypto\Models\UserCryptoWallets;
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
        CovalenthqClient $client,
    ): void {
        $items = $client->fetchTokenQuotes(
            $this->wallet->chain_type,
            $this->wallet->wallet_address,
        );

        $totalCents = 0;
        $tokens = [];

        foreach ($items as $currency) {
            $totalCents += $currency->quoteCents;
            $tokens[$currency->symbol] = $currency->quoteCents;
        }

        $this->wallet->balance_cents = $totalCents;
        $this->wallet->tokens = $tokens;

        $this->wallet->save();
    }
}
