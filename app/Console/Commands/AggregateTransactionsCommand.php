<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessTransactionAggregationJob;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class AggregateTransactionsCommand extends Command
{
    protected $signature = 'app:aggregate-transactions {from?}';

    protected $description = 'Aggregate tagged transactions for all users';

    public function handle(): void
    {
        $from = $this->argument('from') !== null
            ? CarbonImmutable::parse($this->argument('from'))
            : null;

        $users = User::with('subscriptions')
            ->where('demo', '=', false)
            ->get();

        foreach ($users as $user) {
            if (! $user->subscribed()) {
                continue;
            }

            ProcessTransactionAggregationJob::dispatch($user, $from);
        }
    }
}
