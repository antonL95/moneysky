<?php

declare(strict_types=1);

use App\Actions\StockMarket\DeleteStockMarket;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserStockMarket;

use function Pest\Laravel\assertDatabaseCount;

it('successfully deletes stock market ticker', function () {

    Queue::fake();
    $ticker = UserStockMarket::factory()->create();

    $action = app(DeleteStockMarket::class);
    assertDatabaseCount('user_stock_markets', 1);
    $action->handle($ticker);
    assertDatabaseCount('user_stock_markets', 0);
    Queue::assertPushed(ProcessSnapshotJob::class);
});
