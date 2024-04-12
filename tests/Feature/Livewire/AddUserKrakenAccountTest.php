<?php

declare(strict_types=1);

use App\Livewire\AddUserKrakenAccount;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders successfully', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::actingAs($user)
        ->test(AddUserKrakenAccount::class)
        ->set('form.api_key', '123')
        ->set('form.private_key', 'secret')
        ->call('create')
        ->assertStatus(200);

    expect(UserKrakenAccount::all()->count())->toBe(1)
        ->and(UserKrakenAccount::first()->api_key)->toBe('123');
});
