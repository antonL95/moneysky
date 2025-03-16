<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\TransactionAggregate\RecalculateTransactionAggregationAfterTransactionUpdate;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class RecalculateTransactionAggregatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly User $user,
        private readonly UserTransaction $userTransaction,
    ) {}

    public function handle(RecalculateTransactionAggregationAfterTransactionUpdate $action): void
    {
        $action->handle(
            $this->user,
            $this->userTransaction->transaction_tag_id === null
                ? null
                : TransactionTag::find($this->userTransaction->transaction_tag_id),
            $this->userTransaction->booked_at,
        );
    }
}
