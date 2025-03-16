<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\TransactionAggregate\CreateTransactionAggregation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessTransactionAggregationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public function __construct(public User $user, public ?CarbonImmutable $from = null) {}

    public function handle(CreateTransactionAggregation $calculateTransactionAggregation): void
    {
        $calculateTransactionAggregation->handle($this->user, $this->from);
    }
}
