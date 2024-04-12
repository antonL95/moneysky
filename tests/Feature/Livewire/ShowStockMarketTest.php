<?php

declare(strict_types=1);

use App\Livewire\ShowStockMarket;
use App\Models\User;
use App\Models\UserStockMarket;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    UserStockMarket::factory()->count(2)->create(
        ['user_id' => $user->id]
    );

    Livewire::actingAs($user)
        ->test(ShowStockMarket::class)
        ->assertViewHas('headers')
        ->assertViewHas('rows')
        ->assertStatus(200);
});
