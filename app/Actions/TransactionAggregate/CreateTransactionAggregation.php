<?php

declare(strict_types=1);

namespace App\Actions\TransactionAggregate;

use App\Models\TransactionTag;
use App\Models\User;
use Carbon\CarbonImmutable;

final readonly class CreateTransactionAggregation
{
    public function __construct(
        private CalculateTransactionAggregation $calculateTransactionAggregation,
    ) {}

    public function handle(User $user, ?CarbonImmutable $from = null): void
    {
        $now = CarbonImmutable::now();
        $tags = TransactionTag::all();

        $days = $from instanceof CarbonImmutable ? (int) $now->diffInDays($from, true) : 0;

        if ($days > 0) {
            for ($i = $days; $i >= 0; $i--) {
                foreach ($tags as $tag) {
                    $this->calculateTransactionAggregation->handle(
                        $user,
                        $tag,
                        $now->subDays($i),
                    );
                }

                $this->calculateTransactionAggregation->handle(
                    $user,
                    null,
                    $now->subDays($i),
                );
            }
        } else {
            foreach ($tags as $tag) {
                $this->calculateTransactionAggregation->handle(
                    $user,
                    $tag,
                    $now,
                );
            }

            $this->calculateTransactionAggregation->handle(
                $user,
                null,
                $now,
            );
        }
    }
}
