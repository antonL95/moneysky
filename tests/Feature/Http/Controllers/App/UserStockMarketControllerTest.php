<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserStockMarket;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
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

it('can view stock market entries list with proper data transformation', function () {
    $userStockMarket = UserStockMarket::factory()->create([
        'user_id' => $this->user->id,
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance_cents' => 100000,
    ]);

    actingAs($this->user);

    $response = get(route('stock-market.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('stock-market/index')
        ->has('columns', 4)
        ->has('rows', 1)
        ->where('columns', [
            'Id',
            'Ticker',
            'Amount',
            'Balance',
        ])
    );
});

it('cant view stock market entries list without subscription', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    actingAs($user);

    get(route('stock-market.index'))
        ->assertRedirect(route('subscribe'));
});

it('cant view stock market entries list when not authenticated', function () {
    get(route('stock-market.index'))
        ->assertRedirect(route('login'));
});

it('can create stock market entry with authorization check', function () {
    actingAs($this->user);

    post(route('stock-market.store'), [
        'ticker' => 'AAPL',
        'amount' => 10,
    ]);

    assertDatabaseCount('user_stock_markets', 1);

    expect($this->user->userStockMarket->first()->ticker)
        ->toBe('AAPL')
        ->and($this->user->userStockMarket->first()->amount)
        ->toBe(10.0);
});

it('cant create stock market entry without subscription', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    actingAs($user);

    post(route('stock-market.store'), [
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance' => 1000.00,
    ])->assertRedirect(route('subscribe'));

    assertDatabaseCount('user_stock_markets', 0);
});

it('can update stock market entry with authorization check', function () {
    $userStockMarket = $this->user->userStockMarket()->create([
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance_cents' => 100000,
    ]);

    actingAs($this->user);

    put(
        route('stock-market.update', ['stock_market' => $userStockMarket->id]),
        [
            'ticker' => 'GOOGL',
            'amount' => 20,
        ],
    )
        ->assertSessionHas('flash');

    expect($this->user->userStockMarket->first()->ticker)
        ->toBe('GOOGL')
        ->and($this->user->userStockMarket->first()->amount)
        ->toBe(20.0);
});

it('cant update stock market entry of another user', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userStockMarket = UserStockMarket::factory()->create([
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance_cents' => 100000,
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    put(
        route(
            'stock-market.update',
            ['stock_market' => $userStockMarket->id],
        ),
        [
            'ticker' => 'GOOGL',
            'amount' => 20,
        ],
    )->assertStatus(404);

    expect(UserStockMarket::withoutGlobalScopes()
        ->where('user_id', $user2->id)->first()->ticker)
        ->toBe('AAPL');
});

it('can delete stock market entry with authorization check', function () {
    $userStockMarket = $this->user->userStockMarket()->create([
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance_cents' => 100000,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_stock_markets', 1);

    delete(
        route(
            'stock-market.destroy',
            ['stock_market' => $userStockMarket->id],
        ),
    )->assertSessionHas('flash');

    assertDatabaseCount('user_stock_markets', 0);
});

it('cant delete stock market entry of another user', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userStockMarket = UserStockMarket::factory()->create([
        'ticker' => 'AAPL',
        'amount' => 10,
        'balance_cents' => 100000,
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_stock_markets', 1);

    delete(
        route(
            'stock-market.destroy',
            ['stock_market' => $userStockMarket->id],
        ),
    )->assertStatus(404);

    assertDatabaseCount('user_stock_markets', 1);
});
