<?php

declare(strict_types=1);

use App\Livewire\UpdateUserKrakenAccount;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Livewire\Livewire;

it('renders successfully', function () {
    $user = User::factory()->create();

    $userKrakenAccount = UserKrakenAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(UpdateUserKrakenAccount::class, [
            'account' => $userKrakenAccount,
        ])
        ->set('form.api_key', 'new-api-key')
        ->call('update', $userKrakenAccount)
        ->assertStatus(200);

    expect($userKrakenAccount->refresh()->api_key)->toBe('new-api-key');
});
