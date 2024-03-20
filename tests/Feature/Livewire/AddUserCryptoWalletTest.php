<?php

declare(strict_types=1);

use App\Crypto\Enums\ChainType;
use App\Crypto\Models\UserCryptoWallets;
use App\Livewire\AddUserCryptoWallet;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

it('happy path', function () {
    $user = User::factory()->create();

    actingAs($user);
    expect(UserCryptoWallets::all()->count())->toBe(0);

    Livewire::actingAs($user)
        ->test(AddUserCryptoWallet::class)
        ->set('form.wallet_address', 'My Wallet')
        ->set('form.chain_type', ChainType::BTC->value)
        ->call('create')
        ->assertRedirect(route('app.crypto-wallets'));

    expect(UserCryptoWallets::all()->count())->toBe(1)->and(
        UserCryptoWallets::first()->wallet_address,
    )->toBe('My Wallet');
});
