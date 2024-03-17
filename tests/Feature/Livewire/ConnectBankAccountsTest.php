<?php

declare(strict_types=1);

use App\Livewire\ConnectBankAccount;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(ConnectBankAccount::class)
        ->assertStatus(200);
});
