<?php

declare(strict_types=1);

use App\Livewire\ShowStockMarket;
use App\MarketData\Models\UserStockMarket;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    UserStockMarket::factory()->count(2)->create(
        ['user_id' => $user->id]
    );

    Livewire::actingAs($user)
        ->test(ShowStockMarket::class)
        ->assertViewHas('tickers', fn ($accounts) => \count($accounts) === 2)
        ->assertStatus(200);
});
