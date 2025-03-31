<?php

declare(strict_types=1);

use App\Enums\ChainType;
use App\Enums\FlashMessageType;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserCryptoWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'demo' => false,
    ]);
    $this->cryptoWallet = UserCryptoWallet::factory()->create([
        'user_id' => $this->user->id,
    ]);
    Subscription::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('shows crypto wallets list for subscribed user', function () {
    actingAs($this->user);

    $response = get(route('digital-wallet.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('crypto-wallet/index')
        ->has('columns', 4)
        ->has('rows', 1)
        ->has('rows.0', fn (AssertableInertia $row) => $row
            ->where('id', $this->cryptoWallet->id)
            ->where('walletAddress', $this->cryptoWallet->wallet_address)
            ->where('chainType', $this->cryptoWallet->chain_type)
            ->where('balance', $this->cryptoWallet->balance)
            ->etc(),
        ),
    );
});

it('redirects to login when not authenticated', function () {
    $response = get(route('digital-wallet.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

it('redirects to subscription page when user cannot add more resources', function () {
    $user = User::factory()->create([
        'demo' => true,
    ]);

    actingAs($user);

    $response = get(route('digital-wallet.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('subscribe'));
});

it('creates a crypto wallet', function () {
    Queue::fake();
    actingAs($this->user);

    $response = post(route('digital-wallet.store'), [
        'address' => '0x1234567890abcdef',
        'chainType' => ChainType::ETH->value,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Crypto Wallet creation successful',
    ]);

    expect(UserCryptoWallet::count())->toBe(2)
        ->and(UserCryptoWallet::latest('id')->first())
        ->wallet_address->toBe('0x1234567890abcdef')
        ->chain_type->toBe(ChainType::ETH);

    Queue::assertPushed(ProcessCryptoWalletsJob::class);
});

it('prevents creating crypto wallet for unauthorized user', function () {
    $user = User::factory()->create([
        'demo' => true,
    ]);

    actingAs($user);

    $response = post(route('digital-wallet.store'), [
        'address' => '0x1234567890abcdef',
        'chainType' => ChainType::ETH->value,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('subscribe'));

    expect(UserCryptoWallet::count())->toBe(0);
});

it('deletes a crypto wallet', function () {
    $queue = Queue::fake();
    actingAs($this->user);

    $response = delete(route('digital-wallet.destroy', $this->cryptoWallet));

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Crypto Wallet deletion successful',
    ]);

    expect(UserCryptoWallet::count())->toBe(0);
    $queue->assertPushed(ProcessSnapshotJob::class);
});

it('prevents deleting crypto wallet of another user', function () {
    $otherUser = User::factory()->create();
    $otherWallet = UserCryptoWallet::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    actingAs($this->user);

    $response = delete(route('digital-wallet.destroy', $otherWallet));

    $response->assertStatus(404);

    expect(UserCryptoWallet::count())->toBe(1);
});
