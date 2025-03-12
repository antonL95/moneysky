<?php

declare(strict_types=1);

use App\Enums\ChainType;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Models\User;
use App\Models\UserCryptoWallet;
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

it('can create crypto wallet', function () {
    actingAs($this->user);

    post(
        route('digital-wallet.store'),
        [
            'address' => '0xasdfasdf1234asdfadf',
            'chainType' => ChainType::ETH->value,
        ],
    )->assertSessionHas('flash');

    Queue::assertPushed(ProcessCryptoWalletsJob::class);
    assertDatabaseCount('user_crypto_wallets', 1);
    expect($this->user->userCryptoWallet->first()->wallet_address)->toBe('0xasdfasdf1234asdfadf');
});

it('cant create crypto wallet', function () {
    $user = User::factory()->create([
        'demo' => false,
    ]);
    actingAs($user);

    post(route('digital-wallet.store'), [
        'address' => '0xasdfasdf1234asdfadf',
        'chainType' => ChainType::ETH->value,
    ])->assertRedirect(route('subscribe'));

    assertDatabaseCount('user_crypto_wallets', 0);
});

it('can update crypto wallet', function () {
    Queue::fake();

    $digitalWallet = $this->user->userCryptoWallet()->create([
        'wallet_address' => '0xasdfasdf1234asdfadf',
        'chain_type' => ChainType::ETH->value,
    ]);

    actingAs($this->user);

    put(
        route('digital-wallet.update', ['digital_wallet' => $digitalWallet->id]),
        [
            'address' => '0xasdfasdf1234asdfadf',
            'chainType' => ChainType::BTC->value,
        ],
    )->assertSessionHas('flash');

    expect($this->user->userCryptoWallet->first()->chain_type->value)->toBe(ChainType::BTC->value);
});

it('cant update crypto wallet', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userCryptoWallet = UserCryptoWallet::factory()->create([
        'wallet_address' => '0xasdfasdf1234asdfadf',
        'chain_type' => ChainType::ETH->value,
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    put(
        route(
            'digital-wallet.update',
            ['digital_wallet' => $userCryptoWallet->id],
        ),
        [
            'walletAddress' => '0xasdfasdf1234asdfadf',
            'chainType' => 120,
        ],
    )->assertStatus(404);

    expect(
        UserCryptoWallet::withoutGlobalScopes()
            ->where('user_id', $user2->id)->first()->chain_type->value,
    )
        ->toBe(ChainType::ETH->value);
});

it('can delete crypto wallet', function () {
    $userCryptoWallet = $this->user->userCryptoWallet()->create([
        'wallet_address' => '0xasdfasdf1234asdfadf',
        'chain_type' => ChainType::ETH->value,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_crypto_wallets', 1);

    delete(
        route(
            'digital-wallet.destroy',
            ['digital_wallet' => $userCryptoWallet->id],
        ),
    )
        ->assertSessionHas('flash');

    assertDatabaseCount('user_crypto_wallets', 0);
});

it('cant delete crypto wallet', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $userCryptoWallet = UserCryptoWallet::factory()->create([
        'wallet_address' => '0xasdfasdf1234asdfadf',
        'chain_type' => ChainType::ETH->value,
        'user_id' => $user2->id,
    ]);

    actingAs($this->user);

    assertDatabaseCount('user_crypto_wallets', 1);

    delete(
        route(
            'digital-wallet.destroy',
            ['digital_wallet' => $userCryptoWallet->id],
        ),
    )
        ->assertStatus(404);

    assertDatabaseCount('user_crypto_wallets', 1);
});
