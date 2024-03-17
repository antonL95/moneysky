<?php

declare(strict_types=1);

use App\Livewire\UpdateUserStockMarket;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(UpdateUserStockMarket::class)
        ->assertStatus(200);
});
