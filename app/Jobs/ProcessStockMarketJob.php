<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Models\UserStockMarket;
use App\Services\StockMarketService;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessStockMarketJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserStockMarket $userStockMarket,
    ) {}

    /**
     * @throws Exception
     */
    public function handle(
        StockMarketService $client,
    ): void {
        $price = $client->fetchPriceForTicker($this->userStockMarket->ticker);

        $this->userStockMarket->balance_cents = $price;
        $this->userStockMarket->save();
        $user = $this->userStockMarket->user;

        if (! $user instanceof User) {
            return;
        }

        ProcessSnapshotJob::dispatch($user);
    }
}
