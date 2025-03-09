<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Models\UserPortfolioSnapshot;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessSnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly ?User $user,
    ) {}

    public function handle(): void
    {
        if (! $this->user instanceof User) {
            return;
        }

        $now = CarbonImmutable::now();

        $snapshot = UserPortfolioSnapshot::withoutGlobalScopes()->firstOrCreate([
            'aggregate_date' => $now->toDateString(),
            'user_id' => $this->user->id,
        ], [
            'balance_cents' => 0,
            'change' => 0,
        ]);

        ProcessSnapshotBalancesJob::dispatch($snapshot, $this->user);
    }
}
