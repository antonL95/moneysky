<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Data\App\Dashboard\BudgetData;
use App\Jobs\CalculateBudgetJob;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUserBudget
{
    public function handle(UserBudget $budget, BudgetData $data): void
    {
        DB::transaction(static function () use ($data, $budget): void {
            $budget->update([
                'name' => $data->name,
                'balance_cents' => $data->balance * 100,
                'currency' => $data->currency,
            ]);

            if ($data->tags !== null) {
                $budget->tags()->sync($data->tags);
            } else {
                $budget->tags()->sync([]);
            }

            DB::afterCommit(fn () => $budget->periods->each(
                fn (UserBudgetPeriod $userBudgetPeriod) => CalculateBudgetJob::dispatch($userBudgetPeriod),
            ));
        });
    }
}
