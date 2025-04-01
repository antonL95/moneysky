<?php

declare(strict_types=1);

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
});

it('validates required fields', function () {
    $request = new LoginRequest();
    $request->merge([]);

    expect(fn () => $request->authenticate())
        ->toThrow(ValidationException::class)
        ->and(fn () => $request->authenticate())
        ->toThrow(fn (ValidationException $e) => $e->errors()['email'][0] === __('validation.required', ['attribute' => 'email']));
});

it('validates email format', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'not-an-email',
        'password' => 'password',
    ]);

    expect(fn () => $request->authenticate())
        ->toThrow(ValidationException::class)
        ->and(fn () => $request->authenticate())
        ->toThrow(fn (ValidationException $e) => $e->errors()['email'][0] === __('validation.email', ['attribute' => 'email']));
});

it('authenticates user with valid credentials', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $request->authenticate();

    expect(Auth::check())->toBeTrue()
        ->and(Auth::user()->id)->toBe($this->user->id);
});

it('fails authentication with invalid credentials', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    expect(fn () => $request->authenticate())
        ->toThrow(ValidationException::class)
        ->and(fn () => $request->authenticate())
        ->toThrow(fn (ValidationException $e) => $e->errors()['email'][0] === __('auth.failed'));
});

it('respects remember me option', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'password',
        'remember' => true,
    ]);

    $request->authenticate();

    expect(Auth::user()->getRememberToken())->not->toBeNull();
});

it('handles rate limiting', function () {
    $request = new LoginRequest();
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    // Attempt login 6 times (5 is the limit)
    for ($i = 0; $i < 6; $i++) {
        try {
            $request->authenticate();
        } catch (ValidationException) {
            // Expected exception
        }
    }

    expect(RateLimiter::tooManyAttempts($request->throttleKey(), 5))->toBeTrue();
});
