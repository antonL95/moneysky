<?php

declare(strict_types=1);

use App\Livewire\ShowKrakenAccounts;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()
        ->has(
            UserKrakenAccount::factory()->count(3),
        )
        ->create();

    Livewire::actingAs($user)->test(ShowKrakenAccounts::class)
        ->assertViewHas('headers')
        ->assertViewHas('rows')
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
        ->assertViewHas('headers')
        ->assertViewHas('rows', fn ($rows) => \count($rows) === 2)
        ->assertStatus(200);
});
