<?php

declare(strict_types=1);

namespace App\Actions\TransactionAggregate;

use App\Enums\CacheKeys;
use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserTransactionAggregate;
use App\Services\ConvertCurrencyService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

final readonly class CalculateTransactionAggregation
{
    public function __construct(
        private ConvertCurrencyService $currencyConvertor,
    ) {}

    public function handle(
        User $user,
        ?TransactionTag $tag,
        CarbonImmutable $now,
    ): void {
        $taggedTransactions = UserTransaction::withoutGlobalScopes()
            ->whereUserId($user->id)
            ->whereTransactionTagId($tag?->id)
            ->where('booked_at', '>=', $now->setTime(0, 0)->toDateTimeString())
            ->where('booked_at', '<', $now->setTime(23, 59)->toDateTimeString())
            ->whereHidden(false)
            ->get();

        $sum = 0;

        foreach ($taggedTransactions as $transaction) {
            $sum += $this->calculateSumWithDefaultCurrency($transaction);
        }

        $transactionAggregate = UserTransactionAggregate::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('transaction_tag_id', $tag?->id)
            ->whereDate('aggregate_date', $now)
            ->first();

        if ($transactionAggregate instanceof UserTransactionAggregate) {
            $transactionAggregate->balance_cents = $sum;
            $transactionAggregate->save();
        } else {
            UserTransactionAggregate::withoutGlobalScopes()->create([
                'user_id' => $user->id,
                'transaction_tag_id' => $tag?->id,
                'aggregate_date' => $now,
                'balance_cents' => $sum,
                'change' => 0,
            ]);
        }

        $key = sprintf(
            CacheKeys::TRANSACTION_AGGREGATE->value,
            $user->id,
            $now->format('m-Y'),
        );

        Cache::delete($key);
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
