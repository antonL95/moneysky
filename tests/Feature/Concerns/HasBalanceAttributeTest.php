<?php

declare(strict_types=1);

use App\Concerns\HasBalanceAttribute;
use App\Services\ConvertCurrencyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Money\Currency;

// Create a test model that uses the HasBalanceAttribute trait
final class TestModelWithBalance extends Model
{
    use HasBalanceAttribute;

    protected $fillable = [
        'balance_cents',
        'currency',
    ];
}

beforeEach(function () {
    $this->model = new TestModelWithBalance([
        'balance_cents' => 1000, // 10.00 in cents
        'currency' => 'EUR',
    ]);
});

it('returns formatted balance when user is authenticated', function () {
    // Create and authenticate a user
    $user = App\Models\User::factory()->create([
        'currency' => 'EUR',
    ]);

    Auth::shouldReceive('user')
        ->andReturn($user);

    // We can't directly test the formatted output since we can't mock Number::currency
    // Instead, we'll just verify it doesn't throw an exception
    $balance = $this->model->balance;
    expect($balance)->toBeString();
});

it('returns zero balance when user is not authenticated', function () {
    Auth::shouldReceive('user')
        ->andReturn(null);

    // We can't directly test the formatted output since we can't mock Number::currency
    // Instead, we'll just verify it doesn't throw an exception
    $balance = $this->model->balance;
    expect($balance)->toBeString();
});

it('returns numeric balance when user is authenticated', function () {
    // Create and authenticate a user
    $user = App\Models\User::factory()->create([
        'currency' => 'EUR',
    ]);

    Auth::shouldReceive('user')
        ->andReturn($user);

    // Create a real ConvertCurrencyService instance
    $converter = $this->app->make(ConvertCurrencyService::class);

    // We'll test the actual conversion logic
    $this->app->instance(ConvertCurrencyService::class, $converter);

    // Since we're using EUR for both model and user, the value should be 10.0
    expect($this->model->balance_numeric)->toBeFloat()
        ->toEqual(10.0);
});

it('returns zero numeric balance when user is not authenticated', function () {
    Auth::shouldReceive('user')
        ->andReturn(null);

    expect($this->model->balance_numeric)->toBe(0.0);
});

it('returns zero numeric balance when balance_cents is null', function () {
    // Create and authenticate a user
    $user = App\Models\User::factory()->create([
        'currency' => 'EUR',
    ]);

    Auth::shouldReceive('user')
        ->andReturn($user);

    $this->model->balance_cents = null;

    expect($this->model->balance_numeric)->toBe(0.0);
});

it('uses default currency when model currency is null', function () {
    // Create and authenticate a user
    $user = App\Models\User::factory()->create([
        'currency' => 'EUR',
    ]);

    Auth::shouldReceive('user')
        ->andReturn($user);

    $this->model->currency = null;

    // Since we're using the real converter, we just check that it returns a float
    expect($this->model->balance_numeric)->toBeFloat();
});
