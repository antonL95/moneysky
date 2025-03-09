<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessStockMarketJob;
use App\Models\User;
use App\Models\UserStockMarket;
use Illuminate\Console\Command;

final class ProcessStockMarketCommand extends Command
{
    protected $signature = 'app:process-stock-market';

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

            UserStockMarket::withoutGlobalScopes()->where('user_id', $user->id)->each(
                function (UserStockMarket $userStockMarket): void {
                    ProcessStockMarketJob::dispatch($userStockMarket);
                },
            );
        }
    }
}
