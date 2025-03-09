<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserManualEntry;
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

it('can create manual entry', function () {
    actingAs($this->user);

    post(route('manual-entry.store'), [
        'name' => 'Testing',
        'balance' => 20_000,
        'currency' => 'CZK',
        'description' => null,
    ]);

    assertDatabaseCount('user_manual_entries', 1);

    expect($this->user->userManualEntry->first()->currency)
        ->toBe('CZK')
        ->and($this->user->userManualEntry->first()->balance_cents)
        ->toBe(20_000_00);
});

it('cant create manual entry', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    actingAs($user);

    post(route('manual-entry.store'), [
        'name' => 'Testing',
        'balance' => 20_000,
        'currency' => 'CZK',
        'description' => '',
    ])->assertRedirect(route('subscribe'));

    assertDatabaseCount('user_manual_entries', 0);
});

it('can update manual entry', function () {
    $userManualEntry = $this->user->userManualEntry()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
    ]);

    actingAs($this->user);

    put(
        route('manual-entry.update', ['manual_entry' => $userManualEntry->id]),
        [
            'name' => 'Testing',
            'balance' => 20_000,
            'currency' => 'EUR',
            'description' => null,
        ],
    )
        ->assertSessionHas('flash');

    expect($this->user->userManualEntry->first()->currency)
        ->toBe('EUR')
        ->and($this->user->userManualEntry->first()->balance_cents)
        ->toBe(20_000_00);
});

it('cant update manual entry', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userManualEntry = UserManualEntry::factory()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    put(
        route(
            'manual-entry.update',
            ['manual_entry' => $userManualEntry->id],
        ),
        [
            'name' => 'Testing',
            'balance' => 20_000,
            'currency' => 'EUR',
        ],
    )->assertStatus(404);

    expect(UserManualEntry::withoutGlobalScopes()
        ->where('user_id', $user2->id)->first()->balance_cents)
        ->toBe(20_000_00);
});

it('can delete manual entry', function () {
    $userManualEntry = $this->user->userManualEntry()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_manual_entries', 1);

    delete(
        route(
            'manual-entry.destroy',
            ['manual_entry' => $userManualEntry->id],
        ),
    )->assertSessionHas('flash');

    assertDatabaseCount('user_manual_entries', 0);
});

it('cant delete manual entry', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userManualEntry = UserManualEntry::factory()->create([
        'name' => 'Testing',
        'balance_cents' => 20_000_00,
        'currency' => 'CZK',
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_manual_entries', 1);

    delete(
        route(
            'manual-entry.destroy',
            ['manual_entry' => $userManualEntry->id],
        ),
    )->assertStatus(404);

    assertDatabaseCount('user_manual_entries', 1);
});
