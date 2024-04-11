<?php

declare(strict_types=1);

namespace App\MarketData\Console;

use App\MarketData\Jobs\ProcessStockMarket;
use App\MarketData\Models\UserStockMarket;
use App\Models\User;
use Illuminate\Console\Command;

class StockMarketDataCommand extends Command
{
    protected $signature = 'app:stock-market-data';

    protected $description = 'Process ticker data from stock market API and store it in the database.';

    public function handle(): void
    {
        $users = User::where('demo', '=', false)->get();

        foreach ($users as $user) {
            UserStockMarket::withoutGlobalScopes()->where('user_id', $user->id)->each(
                function (UserStockMarket $userStockMarket) {
                    ProcessStockMarket::dispatch($userStockMarket);
                }
            );
        }
    }
}
