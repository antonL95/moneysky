<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\App\Dashboard\TransactionAggregateData;
use App\Data\App\Dashboard\UserTransactionData;
use App\Enums\CacheKeys;
use App\Enums\TransactionType;
use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

final readonly class TransactionsService
{
    /**
     * @return array<int, TransactionAggregateData>
     */
    public function getTransactionAggregates(
        User $user,
        ?string $date,
    ): array {
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

        $tags = TransactionTag::all();

        $spendingData = [];

        foreach ($tags as $tag) {
            $spendingData[$tag->tag] = new TransactionAggregateData(
                $tag->tag,
                (string) Number::currency(0, (string) $user->currency),
                0,
                $tag->id,
            );
        }

        $spendingData['Other'] = new TransactionAggregateData(
            'Other',
            (string) Number::currency(0, (string) $user->currency),
            0,
            null,
        );

        $spendingData['Total'] = new TransactionAggregateData(
            'Total',
            (string) Number::currency(0, (string) $user->currency),
            0,
            'total',
        );

        $transactionAggregates = DB::table('user_transaction_aggregates')
            ->selectRaw(
                'user_transaction_aggregates.transaction_tag_id, SUM(user_transaction_aggregates.balance_cents) as balance_cents',
            )
            ->leftJoin('transaction_tags', 'user_transaction_aggregates.transaction_tag_id', '=', 'transaction_tags.id')
            ->where('user_transaction_aggregates.user_id', $user->id)
            ->whereDate(
                'user_transaction_aggregates.aggregate_date',
                '>=',
                $now->startOfMonth()->toDateString(),
            )
            ->whereDate(
                'user_transaction_aggregates.aggregate_date',
                '<=',
                $now->endOfMonth()->toDateString(),
            )
            ->groupBy('user_transaction_aggregates.transaction_tag_id')
            ->get();

        if ($transactionAggregates->isEmpty()) {
            return array_values($spendingData);
        }

        $totalSum = 0;
        $convertCurrency = new ConvertCurrencyService;

        foreach ($transactionAggregates as $transactionAggregate) {
            $balance = $convertCurrency->convertSimple(
                abs(
                    is_numeric($transactionAggregate->balance_cents)
                        ? (int) $transactionAggregate->balance_cents
                        : 0,
                ) * -1,
                CurrencyHelper::defaultCurrency(),
                $user->currency,
            ) / 100;

            $totalSum += $balance;

            if ($transactionAggregate->transaction_tag_id === null) {
                $spendingData['Other'] = new TransactionAggregateData(
                    'Other',
                    (string) Number::currency($balance, (string) $user->currency),
                    $balance,
                    $transactionAggregate->transaction_tag_id,
                );
            } else {
                // @codeCoverageIgnoreStart
                try {
                    /** @var TransactionTag $transactionTag */
                    $transactionTag = TransactionTag::findOrFail($transactionAggregate->transaction_tag_id);
                } catch (ModelNotFoundException) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                $spendingData[$transactionTag->tag] = new TransactionAggregateData(
                    $transactionTag->tag,
                    (string) Number::currency($balance, (string) $user->currency),
                    $balance,
                    $transactionAggregate->transaction_tag_id, // @phpstan-ignore-line
                );
            }
        }

        $spendingData['Total'] = new TransactionAggregateData(
            'Total',
            (string) Number::currency($totalSum, (string) $user->currency),
            $totalSum,
            'total',
        );

        return array_values($spendingData);
    }

    /**
     * @return array<int, UserTransactionData>
     */
    public function getTransactions(?TransactionTag $tag, ?string $date): array
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

        $user = Auth::user();

        $cacheKey = sprintf(
            CacheKeys::USER_TRANSACTIONS->value,
            $user?->id,
            $tag->id ?? 'other',
            $now->format('Y-m-d'),
        );

        /** @var Collection<int, UserTransaction> $transactions */
        $transactions = Cache::flexible(
            $cacheKey,
            [300, 600],
            static fn (): Collection => UserTransaction::with(['userBankAccount', 'userManualEntry'])
                ->where(
                    'transaction_tag_id',
                    $tag?->id,
                )->where(
                    'booked_at',
                    '>=',
                    $now->startOfMonth()->toDateTimeString(),
                )->where(
                    'booked_at',
                    '<=',
                    $now->endOfMonth()->toDateTimeString(),
                )->get(),
        );

        $result = [];

        foreach ($transactions as $transaction) {
            $result[] = new UserTransactionData(
                $transaction->id,
                $transaction->balance,
                $transaction->balance_numeric,
                Str::limit($transaction->description ?? ''),
                $transaction->currency,
                $transaction->booked_at->format('d/m/Y'),
                $transaction->user_manual_entry_id,
                $transaction->transaction_tag_id,
                $transaction->user_manual_entry_id === null ? TransactionType::AUTOMATIC : TransactionType::MANUAL,
                $transaction->userBankAccount?->name,
                $transaction->userManualEntry?->name,
            );
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public function getHistoricalDates(
        User $user,
    ): array {
        $oldestTransactionAggregate = $user->transactionsAggregate()->oldest('aggregate_date')->first();
        $oldest = $oldestTransactionAggregate !== null
            ? CarbonImmutable::parse($oldestTransactionAggregate->aggregate_date)
            : CarbonImmutable::now();
        $now = CarbonImmutable::now();
        $numberOfMonths = (int) $now->diffInMonths($oldest, absolute: true);
        $result = [];

        for ($i = 0; $i < $numberOfMonths; $i++) {
            $result[] = CarbonImmutable::now()->subMonths($i)->format('m-Y');
        }

        return $result;
    }
}
