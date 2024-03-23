<?php

declare(strict_types=1);

use App\Livewire\UpdateUserManualEntries;
use App\ManualEntry\Models\UserManualEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders successfully', function () {
    $user = User::factory()->create();

    $wallet = UserManualEntry::factory()->create([
        'user_id' => $user->id,
    ]);
    actingAs($user);

    Livewire::actingAs($user)
        ->test(UpdateUserManualEntries::class, ['wallet' => $wallet])
        ->set('form.name', 'Test')
        ->call('update', $wallet)
        ->assertStatus(200);

    expect(UserManualEntry::all()->count())->toBe(1)
        ->and(UserManualEntry::first()->name)->toBe('Test');
});
