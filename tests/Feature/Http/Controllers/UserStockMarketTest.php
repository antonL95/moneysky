<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserStockMarket;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    Queue::fake();
    $this->user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('can create stock market ticker', function () {
    actingAs($this->user);

    post(route('stock-market.store'), [
        'ticker' => 'AAPL',
        'amount' => 100,
    ]);

    assertDatabaseCount('user_stock_markets', 1);

    expect($this->user->userStockMarket->first()->ticker)->toBe('AAPL');
});

it('cant create stock market ticker', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);
    actingAs($user);

    post(route('stock-market.store'), [
        'ticker' => 'AAPL',
        'amount' => 100,
    ])
        ->assertRedirect(route('subscribe'));

    assertDatabaseCount('user_stock_markets', 0);
});

it('can update stock market ticker', function () {
    $userStockMarket = $this->user->userStockMarket()->create([
        'ticker' => 'AAPL',
        'amount' => 100,
    ]);

    actingAs($this->user);

    put(
        route('stock-market.update', ['stock_market' => $userStockMarket->id]),
        [
            'ticker' => 'AAPL',
            'amount' => 120,
        ],
    )
        ->assertSessionHas('flash');

    expect($this->user->userStockMarket->first()->amount)->toBe(120.0);
});

it('cant update stock market ticker', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userStockMarket = UserStockMarket::factory()->create([
        'ticker' => 'AAPL',
        'amount' => 100,
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    put(
        route(
            'stock-market.update',
            ['stock_market' => $userStockMarket->id],
        ),
        [
            'ticker' => 'AAPL',
            'amount' => 120,
        ],
    )->assertStatus(404);

    expect(UserStockMarket::withoutGlobalScopes()
        ->where('user_id', $user2->id)->first()->amount)
        ->toBe(100.0);
});

it('can delete stock market ticker', function () {
    $userStockMarket = $this->user->userStockMarket()->create([
        'ticker' => 'AAPL',
        'amount' => 100,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_stock_markets', 1);

    delete(
        route(
            'stock-market.destroy',
            ['stock_market' => $userStockMarket->id],
        ),
    )
        ->assertSessionHas('flash');

    assertDatabaseCount('user_stock_markets', 0);
});

it('cant delete stock market ticker', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userStockMarket = UserStockMarket::factory()->create([
        'ticker' => 'AAPL',
        'amount' => 100,
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_stock_markets', 1);

    delete(
        route(
            'stock-market.destroy',
            ['stock_market' => $userStockMarket->id],
        ),
    )
        ->assertStatus(404);

    assertDatabaseCount('user_stock_markets', 1);
});
