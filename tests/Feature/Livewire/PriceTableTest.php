<?php

declare(strict_types=1);

use App\Livewire\PriceTable;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(PriceTable::class)
        ->assertSet('plusPrice', '179')
        ->assertSet('unlimitedPrice', '249')
        ->assertStatus(200);
});
