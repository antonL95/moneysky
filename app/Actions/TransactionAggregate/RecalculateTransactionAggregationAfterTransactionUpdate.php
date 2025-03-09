<?php

declare(strict_types=1);

namespace App\Actions\TransactionAggregate;

use App\Models\TransactionTag;
use App\Models\User;
use Carbon\CarbonImmutable;

final readonly class RecalculateTransactionAggregationAfterTransactionUpdate
{
    public function __construct(
        private CalculateTransactionAggregation $calculateTransactionAggregation,
    ) {}

    public function handle(
        User $user,
        ?TransactionTag $tag,
        CarbonImmutable $now,
    ): void {
        $this->calculateTransactionAggregation->handle($user, $tag, $now);
    }
}
