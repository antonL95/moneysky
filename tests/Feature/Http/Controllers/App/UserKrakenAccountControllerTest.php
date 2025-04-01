<?php

declare(strict_types=1);

use App\Enums\FlashMessageType;
use App\Jobs\ProcessKrakenAccountsJob;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    $this->user = User::factory()->create([
        'demo' => false,
    ]);

    $this->krakenAccount = UserKrakenAccount::factory()->create([
        'user_id' => $this->user->id,
        'api_key' => 'test-api-key-123456789',
        'private_key' => 'test-private-key-123456789',
    ]);
    Subscription::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('shows kraken accounts list for subscribed user', function () {
    actingAs($this->user);

    $response = get(route('kraken-account.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('kraken-account/index')
        ->has('columns', 4)
        ->has('rows', 1)
        ->has('rows.0', fn (AssertableInertia $row) => $row
            ->where('id', $this->krakenAccount->id)
            ->where('balance', $this->krakenAccount->balance)
            ->etc(),
        ),
    );
});

it('redirects to login when not authenticated', function () {
    $response = get(route('kraken-account.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

it('redirects to subscription page when user cannot add more resources', function () {
    $user = User::factory()->create([
        'demo' => true,
    ]);

    actingAs($user);

    $response = get(route('kraken-account.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('subscribe'));
});

it('creates a kraken account', function () {
    actingAs($this->user);

    $response = post(route('kraken-account.store'), [
        'apiKey' => 'new-api-key-123456789',
        'privateKey' => 'new-private-key-123456789',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Kraken Account creation successful',
    ]);

    expect(UserKrakenAccount::count())->toBe(2)
        ->and(UserKrakenAccount::latest('id')->first())
        ->api_key->toBe('new-api-key-123456789')
        ->private_key->toBe('new-private-key-123456789');

    Queue::assertPushed(ProcessKrakenAccountsJob::class);
});

it('prevents creating kraken account for unauthorized user', function () {
    $user = User::factory()->create([
        'demo' => true,
    ]);

    actingAs($user);

    $response = post(route('kraken-account.store'), [
        'api_key' => 'new-api-key-123456789',
        'private_key' => 'new-private-key-123456789',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('subscribe'));

    expect(UserKrakenAccount::count())->toBe(0);
});

it('updates a kraken account', function () {
    actingAs($this->user);

    $response = put(route('kraken-account.update', $this->krakenAccount), [
        'apiKey' => 'updated-api-key-123456789',
        'privateKey' => 'updated-private-key-123456789',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Kraken Account update successful',
    ]);

    $this->krakenAccount->refresh();
    expect($this->krakenAccount)
        ->api_key->toBe('updated-api-key-123456789')
        ->private_key->toBe('updated-private-key-123456789');

    Queue::assertPushed(ProcessKrakenAccountsJob::class);
});

it('prevents updating kraken account of another user', function () {
    $otherUser = User::factory()->create();
    $otherAccount = UserKrakenAccount::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    actingAs($this->user);

    $response = put(route('kraken-account.update', $otherAccount), [
        'api_key' => 'updated-api-key-123456789',
        'private_key' => 'updated-private-key-123456789',
    ]);

    $response->assertStatus(404);

    $otherAccount->refresh();
    expect($otherAccount->api_key)->not->toBe('updated-api-key-123456789');
});

it('deletes a kraken account', function () {
    actingAs($this->user);

    $response = delete(route('kraken-account.destroy', $this->krakenAccount));

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Kraken Account deletion successful',
    ]);

    expect(UserKrakenAccount::count())->toBe(0);
    Queue::assertPushed(ProcessSnapshotJob::class);
});

it('prevents deleting kraken account of another user', function () {
    $otherUser = User::factory()->create();
    $otherAccount = UserKrakenAccount::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    actingAs($this->user);

    $response = delete(route('kraken-account.destroy', $otherAccount));

    $response->assertStatus(404);

    expect(UserKrakenAccount::count())->toBe(1);
});

it('handles long api and private keys in index view', function () {
    $longKey = str_repeat('a', 50);
    $this->krakenAccount->update([
        'api_key' => $longKey,
        'private_key' => $longKey,
    ]);

    actingAs($this->user);

    $response = get(route('kraken-account.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('kraken-account/index')
        ->has('rows.0', fn (AssertableInertia $row) => $row
            ->where('apiKey', 'aaaa*********************...')
            ->where('privateKey', 'aaaa*********************...')
            ->etc(),
        ),
    );
});
