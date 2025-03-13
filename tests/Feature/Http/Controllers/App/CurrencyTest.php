<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('updates user currency properly', function () {
    $user = App\Models\User::factory()->create();
    actingAs($user);
    put(route('currency.update'), ['currency' => 'CZK'])
        ->assertStatus(302)
        ->assertSessionHas('flash');

    expect($user->fresh()->currency)->toBe('CZK');
});

test('guest cannot update currency', function () {
    put(route('currency.update'), ['currency' => 'USD'])
        ->assertRedirect(route('login'));
});
