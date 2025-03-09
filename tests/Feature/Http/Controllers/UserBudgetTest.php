<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserBudget;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

it('can create budget', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'unlimited',
    ]);

    actingAs($user);

    post(route('budget.store'), [
        'name' => 'Testing',
        'balance' => 20_000,
        'currency' => 'CZK',
        'tags' => null,
    ]);

    assertDatabaseCount('user_budgets', 1);

    expect($user->budgets->first()->currency)
        ->toBe('CZK')
        ->and($user->budgets->first()->balance_cents)
        ->toBe(20_000_00);
});

it('can update budget', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'unlimited',
    ]);

    $userBudgets = $user->budgets()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
    ]);

    actingAs($user);

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

    expect($user->budgets->first()->currency)
        ->toBe('EUR')
        ->and($user->budgets->first()->balance_cents)
        ->toBe(20_000_00);
});

it('cant update budget', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'unlimited',
    ]);

    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userBudget = UserBudget::factory()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
        'user_id' => $user2->id,
    ]);

    actingAs($user);

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
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'unlimited',
    ]);

    $userBudget = $user->budgets()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
    ]);

    actingAs($user);

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
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'unlimited',
    ]);

    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userBudget = UserBudget::factory()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
        'user_id' => $user2->id,
    ]);

    actingAs($user);

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
