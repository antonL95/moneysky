<?php

declare(strict_types=1);

use App\Console\Commands\ProcessKrakenAccountsCommand;
use App\Jobs\ProcessKrakenAccountsJob;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('processes kraken accounts for subscribed users', function () {
    // Create a subscribed user with kraken accounts
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $krakenAccount1 = UserKrakenAccount::factory()->create([
        'user_id' => $user->id,
        'api_key' => 'test-api-key-1',
        'private_key' => 'test-private-key-1',
    ]);

    $krakenAccount2 = UserKrakenAccount::factory()->create([
        'user_id' => $user->id,
        'api_key' => 'test-api-key-2',
        'private_key' => 'test-private-key-2',
    ]);

    // Create a demo user with kraken accounts (should not be processed)
    $demoUser = User::factory()->create([
        'demo' => true,
    ]);

    Subscription::factory()->create([
        'user_id' => $demoUser->id,
    ]);

    $demoKrakenAccount = UserKrakenAccount::factory()->create([
        'user_id' => $demoUser->id,
        'api_key' => 'test-api-key-3',
        'private_key' => 'test-private-key-3',
    ]);

    // Create an unsubscribed user with kraken accounts (should not be processed)
    $unsubscribedUser = User::factory()->create([
        'demo' => false,
    ]);

    $unsubscribedKrakenAccount = UserKrakenAccount::factory()->create([
        'user_id' => $unsubscribedUser->id,
        'api_key' => 'test-api-key-4',
        'private_key' => 'test-private-key-4',
    ]);

    // Run the command
    artisan(ProcessKrakenAccountsCommand::class)
        ->assertExitCode(0);

    // Assert that only the subscribed user's kraken accounts were processed
    Queue::assertPushed(ProcessKrakenAccountsJob::class, fn ($job) => $job->krakenAccount->id === $krakenAccount1->id);

    Queue::assertPushed(ProcessKrakenAccountsJob::class, fn ($job) => $job->krakenAccount->id === $krakenAccount2->id);

    Queue::assertNotPushed(ProcessKrakenAccountsJob::class, fn ($job) => $job->krakenAccount->id === $demoKrakenAccount->id);

    Queue::assertNotPushed(ProcessKrakenAccountsJob::class, fn ($job) => $job->krakenAccount->id === $unsubscribedKrakenAccount->id);
});
