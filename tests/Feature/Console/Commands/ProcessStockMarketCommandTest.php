<?php

declare(strict_types=1);

use App\Console\Commands\ProcessStockMarketCommand;
use App\Jobs\ProcessStockMarketJob;
use App\Models\User;
use App\Models\UserStockMarket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('processes stock market accounts for subscribed users', function () {
    // Create a subscribed user with stock market accounts
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $stockMarket1 = UserStockMarket::factory()->create([
        'user_id' => $user->id,
        'ticker' => 'AAPL',
        'amount' => 10,
    ]);

    $stockMarket2 = UserStockMarket::factory()->create([
        'user_id' => $user->id,
        'ticker' => 'GOOGL',
        'amount' => 5,
    ]);

    // Create a demo user with stock market accounts (should not be processed)
    $demoUser = User::factory()->create([
        'demo' => true,
    ]);

    Subscription::factory()->create([
        'user_id' => $demoUser->id,
    ]);

    $demoStockMarket = UserStockMarket::factory()->create([
        'user_id' => $demoUser->id,
        'ticker' => 'MSFT',
        'amount' => 15,
    ]);

    // Create an unsubscribed user with stock market accounts (should not be processed)
    $unsubscribedUser = User::factory()->create([
        'demo' => false,
    ]);

    $unsubscribedStockMarket = UserStockMarket::factory()->create([
        'user_id' => $unsubscribedUser->id,
        'ticker' => 'AMZN',
        'amount' => 8,
    ]);

    // Run the command
    artisan(ProcessStockMarketCommand::class)
        ->assertExitCode(0);

    // Assert that only the subscribed user's stock market accounts were processed
    Queue::assertPushed(ProcessStockMarketJob::class, fn ($job) => $job->userStockMarket->id === $stockMarket1->id);

    Queue::assertPushed(ProcessStockMarketJob::class, fn ($job) => $job->userStockMarket->id === $stockMarket2->id);

    Queue::assertNotPushed(ProcessStockMarketJob::class, fn ($job) => $job->userStockMarket->id === $demoStockMarket->id);

    Queue::assertNotPushed(ProcessStockMarketJob::class, fn ($job) => $job->userStockMarket->id === $unsubscribedStockMarket->id);
});
