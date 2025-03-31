<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'demo' => false,
    ]);
});

it('has correct avatar attribute', function () {
    $user = $this->user;

    // Test avatar generation
    $expectedAvatar = 'https://ui-avatars.com/api/?name='.urlencode('J D').'&color=7F9CF5&background=EBF4FF';
    expect($user->avatar)->toBe($expectedAvatar);

    // Test with single name
    $user->name = 'John';
    $user->save();

    $expectedAvatar = 'https://ui-avatars.com/api/?name='.urlencode('J').'&color=7F9CF5&background=EBF4FF';
    expect($user->avatar)->toBe($expectedAvatar);

    // Test with multiple names
    $user->name = 'John Middle Doe';
    $user->save();

    $expectedAvatar = 'https://ui-avatars.com/api/?name='.urlencode('J M D').'&color=7F9CF5&background=EBF4FF';
    expect($user->avatar)->toBe($expectedAvatar);

    // Test with null name
    $user->name = null;
    $user->save();

    $expectedAvatar = 'https://ui-avatars.com/api/?name='.urlencode('').'&color=7F9CF5&background=EBF4FF';
    expect($user->avatar)->toBe($expectedAvatar);
});

it('has correct email_verified attribute', function () {
    $user = $this->user;

    // Test with verified email
    expect($user->email_verified)->toBeTrue();

    // Test with unverified email
    $user->email_verified_at = null;
    $user->save();

    expect($user->email_verified)->toBeFalse();
});

it('has correct is_subscribed attribute', function () {
    $user = $this->user;

    // Test without subscription
    expect($user->is_subscribed)->toBeFalse();

    // Create a subscription with the correct status
    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_status' => 'active',
        'stripe_id' => 'sub_'.Str::random(10),
        'type' => 'default',
    ]);

    // We need to refresh the user to get the updated relationship
    $user->refresh();

    expect($user->is_subscribed)->toBeTrue();
});

it('can check if user can access pulse', function () {
    $user = $this->user;

    // Test regular user
    $user->is_admin = false;
    $user->save();

    expect($user->canAccessPulse())->toBeFalse();

    // Test admin user
    $user->is_admin = true;
    $user->save();

    expect($user->canAccessPulse())->toBeTrue();
});

it('has correct relationships', function () {
    $user = $this->user;

    // Test relationships exist
    expect($user->userBankAccount())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->userCryptoWallet())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->userKrakenAccount())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->userStockMarket())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->userBankTransactions())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasManyThrough::class)
        ->and($user->userBankSession())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->userTransaction())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->userManualEntry())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->dailySnapshots())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->assetSnapshots())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->transactionsAggregate())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->budgets())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($user->socialProviders())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});
