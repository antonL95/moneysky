<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\CalculateSnapshotBalances;
use App\Models\User;
use App\Models\UserPortfolioSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessSnapshotBalancesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserPortfolioSnapshot $snapshot,
        public User $user,
    ) {}

    public function handle(CalculateSnapshotBalances $calculateSnapshotBalances): void
    {
        $calculateSnapshotBalances->handle($this->snapshot, $this->user);
    }
}
