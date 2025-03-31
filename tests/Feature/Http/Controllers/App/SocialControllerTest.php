<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserSocialProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.google.client_id', 'test-client-id');
    Config::set('services.google.client_secret', 'test-client-secret');
    Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
});

it('redirects to social provider', function () {
    $response = get(route('social.redirect', ['driver' => 'google']));

    $response->assertStatus(302);
    $response->assertRedirect();
});

it('creates new user when social login is successful', function () {
    // Mock Socialite user
    $socialiteUser = new SocialiteUser();
    $socialiteUser->id = '123456789';
    $socialiteUser->name = 'Test User';
    $socialiteUser->email = 'test@example.com';
    $socialiteUser->avatar = 'https://example.com/avatar.jpg';
    $socialiteUser->token = 'test-token';
    $socialiteUser->refreshToken = 'test-refresh-token';
    $socialiteUser->expiresIn = 3600;
    $socialiteUser->user = ['extra' => 'data'];

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($socialiteUser);

    // Run the callback
    $response = get(route('social.callback', ['driver' => 'google']));

    // Assert user was created
    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->not->toBeNull();

    // Assert social provider was created
    $socialProvider = UserSocialProvider::where('provider_user_id', '123456789')->first();
    expect($socialProvider)->not->toBeNull()
        ->and($socialProvider->provider_slug)->toBe('google')
        ->and($socialProvider->nickname)->toBeNull()
        ->and($socialProvider->name)->toBe('Test User')
        ->and($socialProvider->email)->toBe('test@example.com')
        ->and($socialProvider->avatar)->toBe('https://example.com/avatar.jpg')
        ->and($socialProvider->token)->toBe('test-token')
        ->and($socialProvider->refresh_token)->toBe('test-refresh-token')
        ->and($socialProvider->token_expires_at)->not->toBeNull()
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);

    // Assert user is logged in

    // Assert redirect to dashboard
    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));
});

it('links existing user when social login with existing email', function () {
    // Create existing user
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Existing User',
    ]);

    // Mock Socialite user
    $socialiteUser = new SocialiteUser();
    $socialiteUser->id = '123456789';
    $socialiteUser->name = 'Test User';
    $socialiteUser->email = 'test@example.com';
    $socialiteUser->avatar = 'https://example.com/avatar.jpg';
    $socialiteUser->token = 'test-token';
    $socialiteUser->refreshToken = 'test-refresh-token';
    $socialiteUser->expiresIn = 3600;
    $socialiteUser->user = ['extra' => 'data'];

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($socialiteUser);

    // Run the callback
    $response = get(route('social.callback', ['driver' => 'google']));

    // Assert user was not created
    expect(User::count())->toBe(1);

    // Assert social provider was created
    $socialProvider = UserSocialProvider::where('provider_user_id', '123456789')->first();
    expect($socialProvider)->not->toBeNull()
        ->and($socialProvider->user_id)->toBe($user->id)
        ->and($socialProvider->provider_slug)->toBe('google')
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);

    // Assert user is logged in

    // Assert redirect to dashboard
    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));
});

it('returns error when social login fails', function () {
    // Mock Socialite to throw an exception
    Socialite::shouldReceive('driver->user')
        ->once()
        ->andThrow(new Exception('Social login failed'));

    // Run the callback
    $response = get(route('social.callback', ['driver' => 'google']));

    // Assert redirect to login with error message
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'An error occurred during authentication. Please try again.');
});

it('prevents linking social account when user already has a different provider', function () {
    // Create existing user with a different social provider
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Existing User',
    ]);

    UserSocialProvider::factory()->create([
        'user_id' => $user->id,
        'provider_slug' => 'facebook',
        'provider_user_id' => '987654321',
    ]);

    // Mock Socialite user
    $socialiteUser = new SocialiteUser();
    $socialiteUser->id = '123456789';
    $socialiteUser->name = 'Test User';
    $socialiteUser->email = 'test@example.com';
    $socialiteUser->avatar = 'https://example.com/avatar.jpg';
    $socialiteUser->token = 'test-token';
    $socialiteUser->refreshToken = 'test-refresh-token';
    $socialiteUser->expiresIn = 3600;
    $socialiteUser->user = ['extra' => 'data'];

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($socialiteUser);

    // Run the callback
    $response = get(route('social.callback', ['driver' => 'google']));

    // Assert redirect to login with error message
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'This email is already associated with another provider. Please login using that provider.');

    // Assert no new social provider was created
    expect(UserSocialProvider::count())->toBe(1);
});
