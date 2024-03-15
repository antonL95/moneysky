<?php

declare(strict_types=1);

namespace App\Crypto\Console;

use App\Crypto\Jobs\ProcessKrakenAccounts;
use App\Crypto\Models\UserKrakenAccount;
use Illuminate\Console\Command;

class KrakenAccountBalanceCommand extends Command
{
    protected $signature = 'app:kraken-account-balance';

    protected $description = 'Get account balance from Kraken';

    public function handle(): int
    {
        UserKrakenAccount::withoutGlobalScopes()->each(function (UserKrakenAccount $krakenAccount) {
            ProcessKrakenAccounts::dispatch($krakenAccount);
        });

        return 0;
    }
}
