<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessCryptoWalletsJob;
use App\Models\User;
use App\Models\UserCryptoWallet;
use Illuminate\Console\Command;

final class ProcessCryptoWalletsCommand extends Command
{
    protected $signature = 'app:process-crypto-wallets';

    protected $description = 'Process raw bank transactions and tag them with categories';

    public function handle(): void
    {
        $users = User::with('subscriptions')
            ->where('demo', '=', false)->get();

        /** @var User $user */
        foreach ($users as $user) {
            if (! $user->subscribed()) {
                continue;
            }

            UserCryptoWallet::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->each(
                    function (UserCryptoWallet $wallet): void {
                        ProcessCryptoWalletsJob::dispatch($wallet);
                    },
                );
        }
    }
}
