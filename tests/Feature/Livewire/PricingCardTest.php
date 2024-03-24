<?php

use App\Livewire\PricingCard;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(PricingCard::class)
        ->assertStatus(200);
});
