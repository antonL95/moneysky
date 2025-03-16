<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CalculateBudgetJob;
use App\Models\UserBudgetPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class CalculateBudgetsCommand extends Command
{
    protected $signature = 'app:calculate-budgets';

    protected $description = 'Calculate all users current budgets';

    public function handle(): void
    {
        $now = CarbonImmutable::now();

        UserBudgetPeriod::whereRaw(
            'DATE(start_date) >= ? AND DATE(end_date) <= ?',
            [$now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()],
        )
            ->get()
            ->each(function (UserBudgetPeriod $userBudgetPeriod): void {
                CalculateBudgetJob::dispatch($userBudgetPeriod);
            });
    }
}
