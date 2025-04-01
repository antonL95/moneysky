<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\BankService;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->bankService = app(BankService::class);
});

it('renders home page for guest users', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('home/index')
    );
});

it('renders home page for authenticated users', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('home/index')
    );
});
