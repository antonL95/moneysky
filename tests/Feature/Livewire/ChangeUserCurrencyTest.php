<?php

declare(strict_types=1);

use App\Livewire\ChangeUserCurrency;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ChangeUserCurrency::class)
        ->assertStatus(200);
});
