<?php

declare(strict_types=1);

use App\Crypto\Models\UserCryptoWallets;
use App\Livewire\ShowCryptoWallets;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    UserCryptoWallets::factory()->count(3)->make([
        'user_id' => $user,
    ]);

    Livewire::actingAs($user)
        ->test(ShowCryptoWallets::class)
        ->assertViewHas('cryptoWallets')
        ->assertStatus(200);
});
