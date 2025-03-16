<?php

declare(strict_types=1);

namespace App\Actions\StockMarket;

use App\Data\App\StockMarket\StockMarketData;
use App\Jobs\ProcessStockMarketJob;
use App\Models\UserStockMarket;

final readonly class UpdateStockMarket
{
    public function handle(UserStockMarket $stockMarket, StockMarketData $data): void
    {
        $stockMarket->update([
            'ticker' => $data->ticker,
            'amount' => $data->amount,
        ]);

        ProcessStockMarketJob::dispatch($stockMarket);
    }
}
