<?php

declare(strict_types=1);

use App\Jobs\ProcessKrakenAccountsJob;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserKrakenAccount;
use App\Services\CryptoExchangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create a Kraken account
    $this->krakenAccount = UserKrakenAccount::factory()->create([
        'user_id' => $this->user->id,
        'api_key' => 'test-api-key',
        'private_key' => 'test-private-key',
        'balance_cents' => 0,
    ]);
    Queue::fake();
});

it('updates kraken account balance and dispatches snapshot job', function () {
    // Create and dispatch the job
    $job = new ProcessKrakenAccountsJob($this->krakenAccount);
    $job->handle(app(CryptoExchangeService::class));

    // Assert that the snapshot job was dispatched
    Queue::assertPushed(ProcessSnapshotJob::class);
});

it('handles case when user is null', function () {
    // Delete the user
    $this->user->delete();

    // Create and dispatch the job
    $job = new ProcessKrakenAccountsJob($this->krakenAccount);
    $job->handle(app(CryptoExchangeService::class));

    // Assert that no snapshot job was dispatched
    Queue::assertNotPushed(ProcessSnapshotJob::class);
});
