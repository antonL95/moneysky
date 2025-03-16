<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\UserBudget;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class CreateBudgetsCommand extends Command
{
    protected $signature = 'app:create-budgets';

    protected $description = 'Create users budgets for new month';

    public function handle(): void
    {
        $now = CarbonImmutable::now();

        UserBudget::withoutGlobalScopes()->get()->each(fn (UserBudget $userBudget) => $userBudget->periods()->create(
            [
                'start_date' => $now->startOfMonth(),
                'end_date' => $now->endOfMonth(),
                'balance_cents' => 0,
            ],
        ));
    }
}
