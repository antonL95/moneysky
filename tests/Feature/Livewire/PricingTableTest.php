<?php

use App\Livewire\PricingTable;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(PricingTable::class)
        ->assertStatus(200);
});
