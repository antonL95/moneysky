<?php

declare(strict_types=1);

namespace App\Crypto\Console;

use App\Crypto\Jobs\ProcessCryptoWallets;
use App\Crypto\Models\UserCryptoWallets;
use Illuminate\Console\Command;

class WalletsBalanceCommand extends Command
{
    protected $signature = 'app:wallets-balance';

    protected $description = 'Fetch and calculate the balance of crypto wallets';

    public function handle(): void
    {
        UserCryptoWallets::withoutGlobalScopes()->each(function (UserCryptoWallets $wallet) {
            ProcessCryptoWallets::dispatch($wallet);
        });
    }
}
