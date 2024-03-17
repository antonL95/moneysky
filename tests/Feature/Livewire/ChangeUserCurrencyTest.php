<?php

declare(strict_types=1);

use App\Livewire\ChangeUserCurrency;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();
    Livewire::actingAs($user)
        ->test(ChangeUserCurrency::class)
        ->assertStatus(200);
});
