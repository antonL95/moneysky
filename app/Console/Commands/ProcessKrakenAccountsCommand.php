<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessKrakenAccountsJob;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Illuminate\Console\Command;

final class ProcessKrakenAccountsCommand extends Command
{
    protected $signature = 'app:process-kraken-accounts';

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

            UserKrakenAccount::withoutGlobalScopes()->where('user_id', $user->id)->each(
                function (UserKrakenAccount $krakenAccount): void {
                    ProcessKrakenAccountsJob::dispatch($krakenAccount);
                },
            );
        }
    }
}
