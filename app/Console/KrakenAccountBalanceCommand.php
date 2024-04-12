<?php

declare(strict_types=1);

namespace App\Console;

use App\Jobs\ProcessKrakenAccounts;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Illuminate\Console\Command;

class KrakenAccountBalanceCommand extends Command
{
    protected $signature = 'app:kraken-account-balance';

    protected $description = 'Get account balance from Kraken';

    public function handle(): void
    {
        $users = User::where('demo', '=', false)->get();

        foreach ($users as $user) {
            UserKrakenAccount::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->each(function (UserKrakenAccount $krakenAccount) {
                    ProcessKrakenAccounts::dispatch($krakenAccount);
                });
        }
    }
}
