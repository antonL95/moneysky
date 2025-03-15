<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Jobs\CalculateBudgetJob;
use App\Jobs\RecalculateTransactionAggregatesJob;
use App\Models\User;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use App\Models\UserManualEntry;
use App\Models\UserTransaction;
use App\Services\ConvertCurrencyService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;

final readonly class AfterTransactionCreateOrUpdate
{
    public function __construct(
        private ConvertCurrencyService $convertCurrency,
    ) {}

    public function handle(User $user, UserTransaction $userTransaction, int $balanceCents, CarbonImmutable $now): void
    {
        try {
            if ($userTransaction->user_manual_entry_id !== null) {
                /** @var UserManualEntry $userManualEntry */
                $userManualEntry = UserManualEntry::findOrFail($userTransaction->user_manual_entry_id);

                if ($userManualEntry->currency !== $userTransaction->currency) {
                    $balanceCents = $this->convertCurrency->convertSimple(
                        $balanceCents,
                        $userTransaction->currency,
                        $userManualEntry->currency,
                    );
                }
                $userManualEntry->decrement('balance_cents', $balanceCents);
            }
            // @codeCoverageIgnoreStart
        } catch (ModelNotFoundException) {
        }
        // @codeCoverageIgnoreEnd

        $this->dispatchJobs($user, $userTransaction, $now);
    }

    public function dispatchJobs(
        User $user,
        UserTransaction $userTransaction,
        CarbonImmutable $now,
    ): void {
        $user->budgets()->with(
            [// @phpstan-ignore-line
                'periods' => fn (HasMany $builder) => $builder->whereRaw(
                    'DATE(start_date) >= ? AND DATE(end_date) <= ?',
                    [$now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()],
                ),
            ],
        )->get()->each(
            fn (UserBudget $userBudget) => $userBudget
                ->periods->each(
                    fn (UserBudgetPeriod $userBudgetPeriod) => CalculateBudgetJob::dispatch($userBudgetPeriod),
                ),
        );

        RecalculateTransactionAggregatesJob::dispatch($user, $userTransaction);
    }
}
