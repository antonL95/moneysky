<?php

declare(strict_types=1);

use App\Livewire\AddUserStockMarket;
use App\Models\User;
use App\Models\UserStockMarket;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders successfully', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::actingAs($user)
        ->test(AddUserStockMarket::class)
        ->set('form.ticker', 'Test')
        ->set('form.amount', '200')
        ->call('create')
        ->assertStatus(200);

    expect(UserStockMarket::all()->count())->toBe(1)
        ->and(UserStockMarket::first()->ticker)->toBe('Test');
});
