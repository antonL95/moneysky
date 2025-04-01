<?php

declare(strict_types=1);

namespace App\Actions\TransactionAggregate;

use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserTransactionAggregate;
use App\Services\ConvertCurrencyService;
use Carbon\CarbonImmutable;

final readonly class CalculateTransactionAggregation
{
    public function __construct(
        private ConvertCurrencyService $currencyConvertor,
    ) {}

    public function handle(
        User $user,
        ?TransactionTag $tag,
        CarbonImmutable $from,
    ): void {
        $taggedTransactions = UserTransaction::withoutGlobalScopes()
            ->whereUserId($user->id)
            ->whereTransactionTagId($tag?->id)
            ->where('booked_at', '>=', $from->startOfDay()->toDateTimeString())
            ->where('booked_at', '<', $from->endOfDay()->toDateTimeString())
            ->whereHidden(false)
            ->get();

        $sum = 0;

        foreach ($taggedTransactions as $transaction) {
            $sum += $this->calculateSumWithDefaultCurrency($transaction);
        }

        $transactionAggregate = UserTransactionAggregate::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('transaction_tag_id', $tag?->id)
            ->whereDate('aggregate_date', $from->toDateString())
            ->first();

        if ($transactionAggregate instanceof UserTransactionAggregate) {
            $transactionAggregate->balance_cents = $sum;
            $transactionAggregate->save();
        } else {
            UserTransactionAggregate::withoutGlobalScopes()->create([
                'user_id' => $user->id,
                'transaction_tag_id' => $tag?->id,
                'aggregate_date' => $from,
                'balance_cents' => $sum,
                'change' => 0,
            ]);
        }
    }

    private function calculateSumWithDefaultCurrency(UserTransaction $transaction): int
    {
        return $this->currencyConvertor->convertSimple(
            $transaction->balance_cents,
            // @codeCoverageIgnoreStart
            $transaction->currency === ''
                ? CurrencyHelper::defaultCurrency()
                : $transaction->currency,
            // @codeCoverageIgnoreEnd
            CurrencyHelper::defaultCurrency(),
        );
    }
}
