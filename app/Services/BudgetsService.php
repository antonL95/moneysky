<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\App\Dashboard\UserBudgetData;
use App\Models\User;
use App\Models\UserBudget;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

final readonly class BudgetsService
{
    public function __construct(
        private ConvertCurrencyService $convertCurrencyService,
    ) {}

    /**
     * @return Collection<int, UserBudgetData>
     */
    public function getBudgets(User $user, ?string $date): Collection
    {
        $now = CarbonImmutable::now();

        if ($date !== null) {
            try {
                $now = CarbonImmutable::createFromFormat('m-Y', $date);
            } catch (Exception) {
                $now = CarbonImmutable::now();
            }
        }

        // @codeCoverageIgnoreStart
        if (! $now instanceof CarbonImmutable) {
            $now = CarbonImmutable::now();
        }
        // @codeCoverageIgnoreEnd

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
                $this->convertCurrencyService->convertSimple(
                    (int) $userBudget->periods->first()?->balance_cents,
                    $userBudget->currency,
                    $user->currency,
                ) / 100,
                $userBudget->balance_numeric,
                $userBudget->currency,
                $userBudget->tags->pluck('id')->toArray(), // @phpstan-ignore-line
                $userBudget->id,
            ));

        return $budgets;
    }
}
