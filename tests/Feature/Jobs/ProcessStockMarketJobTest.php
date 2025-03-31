<?php

declare(strict_types=1);

use App\Exceptions\StockMarketClientException;
use App\Jobs\ProcessSnapshotJob;
use App\Jobs\ProcessStockMarketJob;
use App\Models\User;
use App\Models\UserStockMarket;
use App\Services\StockMarketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create([
        'currency' => 'USD',
    ]);

    // Create a stock market entry for the user
    $this->stockMarket = UserStockMarket::factory()->create([
        'user_id' => $this->user->id,
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance_cents' => 0,
    ]);

    // Set up a fake price in the cache
    Cache::put('stock-market-AAPL', 23907); // $239.07 per share
});

it('updates stock market balance and dispatches snapshot job', function () {
    Queue::fake();

    // Create and dispatch the job
    $job = new ProcessStockMarketJob($this->stockMarket);
    $job->handle(app(StockMarketService::class));

    // Assert that the balance was updated correctly
    // 10 shares * $239.07 = $2390.70 = 239070 cents
    expect($this->stockMarket->fresh()->balance_cents)->toBe(23907)
        ->and($this->stockMarket->fresh()->balance)
        ->toBe(Illuminate\Support\Number::currency(2390.7, 'USD'));

    // Assert that ProcessSnapshotJob was dispatched with the correct user
    Queue::assertPushed(ProcessSnapshotJob::class);
});

it('handles case when user is null', function () {
    Queue::fake();

    // Delete the user
    $this->user->delete();

    // Create and dispatch the job
    $job = new ProcessStockMarketJob($this->stockMarket);
    $job->handle(app(StockMarketService::class));

    // Assert that the balance was still updated
    expect($this->stockMarket?->fresh())->toBeNull();

    // Assert that no snapshot job was dispatched
    Queue::assertNotPushed(ProcessSnapshotJob::class);
});

it('handles invalid cached price', function () {
    // Set up an invalid price in the cache
    Cache::put('stock-market-AAPL', 'invalid_price');

    // Create and dispatch the job
    $job = new ProcessStockMarketJob($this->stockMarket);

    // Assert that the job throws an exception
    $job->handle(app(StockMarketService::class));
})->throws(StockMarketClientException::class, 'Invalid price cached');
