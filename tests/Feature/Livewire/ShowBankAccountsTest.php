<?php

declare(strict_types=1);

use App\Livewire\ShowBankAccounts;
use App\Models\User;
use App\Models\UserBankAccount;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    UserBankAccount::factory()->count(3)->create(
        [
            'user_id' => $user->id,
        ],
    );

    Livewire::actingAs($user)
        ->test(ShowBankAccounts::class)
        ->assertViewHas('headers')
        ->assertViewHas('rows')
        ->assertStatus(200);
});

it('sees only their accounts', function () {
    $userOne = User::factory()->has(
        UserBankAccount::factory()->count(3),
    )->create();

    $otherUser = User::factory()->create();

    UserBankAccount::factory()->count(2)->create(
        [
            'user_id' => $otherUser->id,
        ],
    );

    Livewire::actingAs($otherUser)
        ->test(ShowBankAccounts::class)
        ->assertViewHas('headers')
        ->assertViewHas('rows', fn ($rows) => \count($rows) === 2)
        ->assertStatus(200);
});
