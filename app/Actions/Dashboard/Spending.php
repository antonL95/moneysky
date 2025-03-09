<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Data\SpendingData;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransactionAggregate;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

use function array_key_exists;

final readonly class Spending
{
    /**
     * @return Collection<string, SpendingData>
     */
    public function handle(User $user, CarbonImmutable $now): Collection
    {
        $stats = [];

        $startOfMonth = $now->startOfMonth();
        $assetsSnapshot = UserTransactionAggregate::with('transactionTag')
            ->where(
                'aggregate_date',
                '>=',
                $startOfMonth->toDateString(),
            )->where(
                'aggregate_date',
                '<=',
                $now->endOfMonth(),
            )->get();

        if ($assetsSnapshot->isEmpty()) {
            /* @phpstan-ignore-next-line */
            return collect();
        }

        $sums = [];

        foreach ($assetsSnapshot as $asset) {
            $tagId = $asset->transactionTag->tag ?? 'other';
            if (! array_key_exists($tagId, $sums)) {
                $sums[$tagId] = $asset->balance_numeric ?? 0;
            } else {
                $sums[$tagId] += $asset->balance_numeric ?? 0;
            }
        }

        $totalSum = 0;

        foreach ($sums as $transactionId => $sum) {
            $tag = null;
            if ($transactionId !== 'other') {
                try {
                    $tag = TransactionTag::whereTag($transactionId)->firstOrFail();
                } catch (ModelNotFoundException) {
                    continue;
                }
            }

            $stats[$tag->tag ?? 'Other'] = new SpendingData(
                (string) Number::currency($sum, $user->currency),
                $sum,
                $tag?->id,
            );

            $totalSum += $sum;
        }

        $stats['Total'] = new SpendingData(
            (string) Number::currency($totalSum, $user->currency),
            $totalSum,
            'all',
        );

        return collect($stats);
    }
}
