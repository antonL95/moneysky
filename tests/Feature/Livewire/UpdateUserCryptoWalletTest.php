<?php

declare(strict_types=1);

use App\Crypto\Enums\ChainType;
use App\Crypto\Models\UserCryptoWallets;
use App\Livewire\UpdateUserCryptoWallet;
use App\Models\User;
use Livewire\Livewire;

it('updates successfully', function () {
    $user = User::factory()->create();

    $wallet = UserCryptoWallets::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(UpdateUserCryptoWallet::class, [
            'wallet' => $wallet,
        ])
        ->set('form.chain_type', ChainType::MATIC->value)
        ->call('update', $wallet);

    expect($wallet->refresh()->chain_type)->toBe(ChainType::MATIC);
});
