<?php

declare(strict_types=1);

use App\Enums\ChainType;
use App\Livewire\UpdateUserCryptoWallet;
use App\Models\User;
use App\Models\UserCryptoWallets;
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
