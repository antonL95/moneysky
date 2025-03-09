<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Data\App\Dashboard\BudgetData;
use App\Jobs\CalculateBudgetJob;
use App\Models\User;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final readonly class CreateUserBudget
{
    public function handle(User $user, BudgetData $budgetData): void
    {
        DB::transaction(static function () use ($budgetData, $user): void {
            $userBudget = UserBudget::create([
                'user_id' => $user->id,
                'name' => $budgetData->name,
                'balance_cents' => $budgetData->balance * 100,
                'currency' => $budgetData->currency,
            ]);

            $now = new CarbonImmutable;
            $userBudgetPeriod = UserBudgetPeriod::create([
                'user_budget_id' => $userBudget->id,
                'start_date' => $now->startOfMonth()->toDateString(),
                'end_date' => $now->endOfMonth()->toDateString(),
                'balance_cents' => 0,
            ]);

            if ($budgetData->tags !== null) {
                $userBudget->tags()->sync(collect($budgetData->tags));
            }

            DB::afterCommit(fn () => CalculateBudgetJob::dispatch($userBudgetPeriod));
        });
    }
}
