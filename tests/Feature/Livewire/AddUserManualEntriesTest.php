<?php

declare(strict_types=1);

use App\Livewire\AddUserManualEntries;
use App\ManualEntry\Models\UserManualEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders successfully', function () {
    $user = User::factory()->create();

    actingAs($user);
    Livewire::actingAs($user)
        ->test(AddUserManualEntries::class)
        ->set('form.name', 'Test')
        ->set('form.amount', '200')
        ->set('form.currency', 'USD')
        ->set('form.description', 'Test')
        ->call('create')
        ->assertStatus(200);

    expect(UserManualEntry::all()->count())->toBe(1)
        ->and(UserManualEntry::first()->name)->toBe('Test');
});
