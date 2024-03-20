<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::actingAs(
        User::factory()->create()
    )->test(Dashboard::class)
        ->assertViewHas('headers')
        ->assertViewHas('rows')
        ->assertStatus(200);
});
