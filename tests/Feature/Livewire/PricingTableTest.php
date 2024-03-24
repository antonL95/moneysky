<?php

declare(strict_types=1);

use App\Livewire\PricingTable;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(PricingTable::class)
        ->assertViewHas('amount', '$11.99')
        ->assertStatus(200);
});
