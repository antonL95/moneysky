<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('nullable user is redirected to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    actingAs($this->user);
    $this->get(route('dashboard'))->assertOk();
});

test('authenticated users can visit the dashboard with date', function () {
    actingAs($this->user);
    $this->get(route('dashboard', ['date' => '03/2025']))->assertOk()->assertInertia(
        fn (AssertableInertia $page) => $page->where('selectedDate', '03/2025'),
    );
});
