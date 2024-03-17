<?php

declare(strict_types=1);

use App\Bank\Models\UserBankAccount;
use App\Livewire\ShowBankAccounts;
use App\Models\User;
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
        ->assertViewHas('bankAccounts', fn ($accounts) => \count($accounts) === 3)
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
        ->assertViewHas('bankAccounts', fn ($accounts) => \count($accounts) === 2)
        ->assertStatus(200);
});
