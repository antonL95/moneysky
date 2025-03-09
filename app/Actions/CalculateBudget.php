<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Scopes\UserScope;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use App\Models\UserTransaction;
use App\Services\ConvertCurrencyService;
use Money\Currency;
use Money\Money;

final readonly class CalculateBudget
{
    public function __construct(
        private ConvertCurrencyService $convertCurrency,
    ) {}

    public function handle(
        UserBudgetPeriod $userBudgetPeriod
    ): void {
        /** @var UserBudget|null $userBudget */
        $userBudget = $userBudgetPeriod->userBudget()
            ->getQuery()
            ->withoutGlobalScope(UserScope::class)
            ->with('tags')
            ->first();

        if ($userBudget === null) {
            return;
        }

        $userTransactionsQuery = UserTransaction::withoutGlobalScopes()
            ->whereHidden(false)
            ->whereUserId($userBudget->user_id)
            ->where('booked_at', '>=', $userBudgetPeriod->start_date)
            ->where('booked_at', '<=', $userBudgetPeriod->end_date->setTime(23, 59, 59));

        if ($userBudget->tags->isNotEmpty()) {
            $userTransactionsQuery->whereIn('transaction_tag_id', $userBudget->tags->pluck('id'));
        }

        $userTransactions = $userTransactionsQuery->get();

        $sum = 0;

        foreach ($userTransactions as $userTransaction) {
            /** @var non-empty-string $transactionCurrency */
            $transactionCurrency = $userTransaction->currency;
            /** @var non-empty-string $budgetCurrency */
            $budgetCurrency = $userBudget->currency;

            $balance = (int) $this->convertCurrency->convert(
                new Money($userTransaction->balance_cents, new Currency($transactionCurrency)),
                new Currency($budgetCurrency),
            )->getAmount();

            $sum += abs($balance);
        }

        $userBudgetPeriod->balance_cents = $sum;
        $userBudgetPeriod->save();
    }
}
