<?php

declare(strict_types=1);

use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBudget;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('can create budget', function () {
    actingAs($this->user);

    TransactionTag::factory(10)->create();

    post(route('budget.store'), [
        'name' => 'Testing',
        'balance' => 20_000,
        'currency' => 'CZK',
        'tags' => TransactionTag::inRandomOrder()->take(2)->pluck('id')->toArray(),
    ]);

    assertDatabaseCount('user_budgets', 1);

    expect($this->user->budgets->first()->currency)
        ->toBe('CZK')
        ->and($this->user->budgets->first()->balance_cents)
        ->toBe(20_000_00)
        ->and($this->user->budgets->first()->currency)
        ->toBe('CZK')
        ->and($this->user->budgets->first()->tags)
        ->not
        ->toBeNull();
});

it('can update budget', function () {
    $userBudgets = $this->user->budgets()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
    ]);

    actingAs($this->user);

    put(
        route('budget.update', ['budget' => $userBudgets->id]),
        [
            'name' => 'Testing',
            'balance' => 20_000,
            'currency' => 'EUR',
            'tags' => null,
        ],
    )
        ->assertSessionHas('flash');

    expect($this->user->budgets->first()->currency)
        ->toBe('EUR')
        ->and($this->user->budgets->first()->balance_cents)
        ->toBe(20_000_00);
});

it('cant update budget', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userBudget = UserBudget::factory()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    put(route('budget.update', ['budget' => $userBudget->id]),
        [
            'name' => 'Testing',
            'balance' => 20_000,
            'currency' => 'EUR',
            'tags' => null,
        ],
    )->assertStatus(404);

    expect(UserBudget::withoutGlobalScopes()
        ->where('user_id', $user2->id)->first()->balance_cents)
        ->toBe(20_000_00);
});

it('can delete budget', function () {
    $userBudget = $this->user->budgets()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_budgets', 1);

    delete(
        route(
            'budget.destroy',
            ['budget' => $userBudget->id],
        ),
    )
        ->assertSessionHas('flash');

    assertDatabaseCount('user_budgets', 0);
});

it('cant delete budget', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userBudget = UserBudget::factory()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_budgets', 1);

    delete(
        route(
            'budget.destroy',
            ['budget' => $userBudget->id],
        ),
    )
        ->assertStatus(404);

    assertDatabaseCount('user_budgets', 1);
});
