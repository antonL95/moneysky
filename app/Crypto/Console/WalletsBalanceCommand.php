<?php

declare(strict_types=1);

namespace App\Crypto\Console;

use App\Crypto\Jobs\ProcessCryptoWallets;
use App\Crypto\Models\UserCryptoWallets;
use App\Models\User;
use Illuminate\Console\Command;

class WalletsBalanceCommand extends Command
{
    protected $signature = 'app:wallets-balance';

    protected $description = 'Fetch and calculate the balance of crypto wallets';

    public function handle(): void
    {
        $users = User::where('demo', '=', false)->get();

        foreach ($users as $user) {
            UserCryptoWallets::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->each(function ($wallet) {
                    ProcessCryptoWallets::dispatch($wallet);
                });
        }
    }
}
