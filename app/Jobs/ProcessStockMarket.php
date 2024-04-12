<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\UserStockMarket;
use App\Services\AlphaVantageService;
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
        AlphaVantageService $client,
    ): void {
        $price = $client->fetchPriceForTicker($this->userStockMarket->ticker);

        $this->userStockMarket->price_cents = $price;
        $this->userStockMarket->save();
    }
}
