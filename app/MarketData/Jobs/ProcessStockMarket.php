<?php

declare(strict_types=1);

namespace App\MarketData\Jobs;

use App\MarketData\Contracts\IStockMarketClient;
use App\MarketData\Models\UserStockMarket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStockMarket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserStockMarket $userStockMarket,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(
        IStockMarketClient $stockMarketClient,
    ): void {
        $price = $stockMarketClient->fetchPriceForTicker($this->userStockMarket->ticker);
        $this->userStockMarket->price_cents = (int) ($price * $this->userStockMarket->amount);
        $this->userStockMarket->save();
    }
}
