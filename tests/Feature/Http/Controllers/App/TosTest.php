<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guest can see terms', function () {
    $this->get(route('terms.show'))->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->component('static-page/index')
                ->has('title')
                ->where('title', 'Terms and Conditions'),
        );
});

test('authenticated users can visit see terms', function () {
    actingAs($this->user);
    $this->get(route('terms.show'))->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->component('static-page/index')
                ->has('title')
                ->where('title', 'Terms and Conditions'),
        );
});
