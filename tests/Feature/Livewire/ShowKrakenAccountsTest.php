<?php

declare(strict_types=1);

use App\Crypto\Models\UserKrakenAccount;
use App\Livewire\ShowKrakenAccounts;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()
        ->has(
            UserKrakenAccount::factory()->count(3),
        )
        ->create();

    Livewire::actingAs($user)->test(ShowKrakenAccounts::class)
        ->assertViewHas('krakenAccounts', fn ($accounts) => \count($accounts) === 3)
        ->assertStatus(200);
});

it('user sees only their accounts', function () {
    User::factory()->has(
        UserKrakenAccount::factory()->count(3),
    )->create();

    $otherUser = User::factory()->has(
        UserKrakenAccount::factory()->count(2),
    )->create();

    Livewire::actingAs($otherUser)
        ->test(ShowKrakenAccounts::class)
        ->assertViewHas('krakenAccounts', fn ($accounts) => \count($accounts) === 2)
        ->assertStatus(200);
});
