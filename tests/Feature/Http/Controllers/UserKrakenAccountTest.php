<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserKrakenAccount;
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

it('can create kraken account', function () {
    actingAs($this->user);

    post(route('kraken-account.store'), [
        'apiKey' => '0xasdfasdf1234asdfadf',
        'privateKey' => '0xasdfasdf1234asdfadf',
    ]);

    assertDatabaseCount('user_kraken_accounts', 1);

    expect($this->user->userKrakenAccount->first()->api_key)->toBe('0xasdfasdf1234asdfadf');
});

it('cant create kraken account', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);

    actingAs($user);

    post(route('kraken-account.store'), [
        'api_key' => '0xasdfasdf1234asdfadf',
        'private_key' => '0xasdfasdf1234asdfadf',
    ])->assertRedirect(route('subscribe'));

    assertDatabaseCount('user_kraken_accounts', 0);
});

it('can update kraken account', function () {
    $userKrakenAccount = $this->user->userKrakenAccount()->create([
        'api_key' => '0xasdfasdf1234asdfadf',
        'private_key' => '0xasdfasdf1234asdfadf',
    ]);

    actingAs($this->user);

    put(
        route('kraken-account.update', ['kraken_account' => $userKrakenAccount->id]),
        [
            'apiKey' => '0xasdfasdf1234asdfadfasdf',
            'privateKey' => '0xasdfasdf1234asdfadf',
        ],
    )->assertSessionHas('flash');

    expect($this->user->userKrakenAccount->first()->api_key)->toBe('0xasdfasdf1234asdfadfasdf');
});

it('cant update kraken account', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userKrakenAccount = UserKrakenAccount::factory()->create([
        'api_key' => '0xasdfasdf1234asdfadf',
        'private_key' => '0xasdfasdf1234asdfadf',
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    put(
        route(
            'kraken-account.update',
            ['kraken_account' => $userKrakenAccount->id],
        ),
        [
            'apiKey' => '0xasdfasdf1234asdfadfasdfasdf',
            'privateKey' => '0xasdfasdf1234asdfadf',
        ],
    )->assertStatus(404);

    expect(UserKrakenAccount::withoutGlobalScopes()
        ->where('user_id', $user2->id)->first()->api_key)
        ->toBe('0xasdfasdf1234asdfadf');
});

it('can delete kraken account', function () {
    $userKrakenAccount = $this->user->userKrakenAccount()->create([
        'api_key' => '0xasdfasdf1234asdfadf',
        'private_key' => '0xasdfasdf1234asdfadf',
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_kraken_accounts', 1);

    delete(
        route(
            'kraken-account.destroy',
            ['kraken_account' => $userKrakenAccount->id],
        ),
    )->assertSessionHas('flash');

    assertDatabaseCount('user_kraken_accounts', 0);
});

it('cant delete kraken account', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userKrakenAccount = UserKrakenAccount::factory()->create([
        'api_key' => '0xasdfasdf1234asdfadf',
        'private_key' => '0xasdfasdf1234asdfadf',
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_kraken_accounts', 1);

    delete(
        route(
            'kraken-account.destroy',
            ['kraken_account' => $userKrakenAccount->id],
        ),
    )->assertStatus(404);

    assertDatabaseCount('user_kraken_accounts', 1);
});
