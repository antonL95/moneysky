<?php

declare(strict_types=1);

use App\Livewire\ShowManualEntries;
use App\Models\User;
use App\Models\UserManualEntry;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    UserManualEntry::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(ShowManualEntries::class)
        ->assertViewHas('headers')
        ->assertViewHas('rows')
        ->assertStatus(200);
});
