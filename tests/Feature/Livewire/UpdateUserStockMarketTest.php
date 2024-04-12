<?php

declare(strict_types=1);

use App\Livewire\UpdateUserStockMarket;
use App\Models\User;
use App\Models\UserStockMarket;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    $ticker = UserStockMarket::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(UpdateUserStockMarket::class, [
            'ticker' => $ticker,
        ])
        ->set('form.amount', 69.69)
        ->call('update', $ticker);

    expect($ticker->refresh()->amount)->toBe(69.69);
});
