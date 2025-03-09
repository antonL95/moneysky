<?php

declare(strict_types=1);

namespace App\Actions\StockMarket;

use App\Jobs\ProcessSnapshotJob;
use App\Models\UserStockMarket;

final readonly class DeleteStockMarket
{
    public function handle(UserStockMarket $userStockMarket): void
    {
        $user = $userStockMarket->user;
        if ($user === null) {
            return;
        }

        $userStockMarket->delete();

        ProcessSnapshotJob::dispatch($user);
    }
}
