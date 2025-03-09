<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\App\Dashboard\UserBudgetData;
use App\Models\User;
use App\Models\UserBudget;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

final readonly class BudgetsService
{
    /**
     * @return Collection<int, UserBudgetData>
     */
    public function getBudgets(User $user, ?string $date): Collection
    {
        $now = CarbonImmutable::now();

        if ($date !== null) {
            $now = CarbonImmutable::createFromFormat('m-Y', $date);
        }

        if (! $now instanceof CarbonImmutable) {
            $now = CarbonImmutable::now();
        }

        /* @var Collection<int, UserBudgetData> $budgets */
        $budgets = $user->budgets()->with(
            [
                'periods' => fn (Relation $query): Relation => $query->whereRaw('DATE(start_date) = ?', $now->startOfMonth()->toDateString())
                    ->whereRaw('DATE(end_date) = ?', $now->endOfMonth()->toDateString()),
                'tags',
            ],
        )
            ->get()
            ->filter(
                fn (UserBudget $userBudget): bool => $userBudget->periods->first() !== null,
            )->map(fn (UserBudget $userBudget): UserBudgetData => new UserBudgetData(
                $userBudget->periods->first()->id ?? 0,
                $userBudget->name,
                $userBudget->periods->first()->balance_numeric ?? 0,
                $userBudget->balance_numeric,
                $userBudget->currency,
                $userBudget->tags->pluck('id')->toArray(), // @phpstan-ignore-line
                $userBudget->id,
            ));

        return $budgets;
    }
}
