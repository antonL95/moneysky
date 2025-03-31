<?php

declare(strict_types=1);

use App\Console\Commands\ProcessCryptoWalletsCommand;
use App\Enums\ChainType;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Models\User;
use App\Models\UserCryptoWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('processes crypto wallets for subscribed users', function () {
    // Create a subscribed user with crypto wallets
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $ethWallet = UserCryptoWallet::factory()->create([
        'user_id' => $user->id,
        'chain_type' => ChainType::ETH,
        'wallet_address' => '0x1234567890123456789012345678901234567890',
    ]);

    $btcWallet = UserCryptoWallet::factory()->create([
        'user_id' => $user->id,
        'chain_type' => ChainType::BTC,
        'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
    ]);

    // Create a demo user with crypto wallets (should not be processed)
    $demoUser = User::factory()->create([
        'demo' => true,
    ]);

    Subscription::factory()->create([
        'user_id' => $demoUser->id,
    ]);

    $demoWallet = UserCryptoWallet::factory()->create([
        'user_id' => $demoUser->id,
        'chain_type' => ChainType::ETH,
        'wallet_address' => '0x9876543210987654321098765432109876543210',
    ]);

    // Create an unsubscribed user with crypto wallets (should not be processed)
    $unsubscribedUser = User::factory()->create([
        'demo' => false,
    ]);

    $unsubscribedWallet = UserCryptoWallet::factory()->create([
        'user_id' => $unsubscribedUser->id,
        'chain_type' => ChainType::ETH,
        'wallet_address' => '0xabcdef1234567890abcdef1234567890abcdef12',
    ]);

    // Run the command
    artisan(ProcessCryptoWalletsCommand::class)
        ->assertExitCode(0);

    // Assert that only the subscribed user's wallets were processed
    Queue::assertPushed(ProcessCryptoWalletsJob::class, fn ($job) => $job->wallet->id === $ethWallet->id);

    Queue::assertPushed(ProcessCryptoWalletsJob::class, fn ($job) => $job->wallet->id === $btcWallet->id);

    Queue::assertNotPushed(ProcessCryptoWalletsJob::class, fn ($job) => $job->wallet->id === $demoWallet->id);

    Queue::assertNotPushed(ProcessCryptoWalletsJob::class, fn ($job) => $job->wallet->id === $unsubscribedWallet->id);
});
