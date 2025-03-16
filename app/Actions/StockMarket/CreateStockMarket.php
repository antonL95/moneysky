<?php

declare(strict_types=1);

namespace App\Actions\StockMarket;

use App\Data\App\StockMarket\StockMarketData;
use App\Jobs\ProcessStockMarketJob;
use App\Models\User;

final readonly class CreateStockMarket
{
    public function handle(User $user, StockMarketData $data): void
    {
        $stockMarket = $user->userStockMarket()->create([
            'ticker' => $data->ticker,
            'amount' => $data->amount,
        ]);

        ProcessStockMarketJob::dispatch($stockMarket);
    }
}
