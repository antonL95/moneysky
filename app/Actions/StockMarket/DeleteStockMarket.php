<?php

declare(strict_types=1);

namespace App\Actions\StockMarket;

use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserStockMarket;

final readonly class DeleteStockMarket
{
    public function handle(UserStockMarket $userStockMarket): void
    {
        /** @var User $user */
        $user = $userStockMarket->user;

        $userStockMarket->delete();

        ProcessSnapshotJob::dispatch($user);
    }
}
