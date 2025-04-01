<?php

declare(strict_types=1);

use App\Jobs\ProcessSnapshotBalancesJob;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserPortfolioSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();
});

it('creates a snapshot and dispatches balance calculation job', function () {
    Queue::fake();

    // Create and dispatch the job
    $job = new ProcessSnapshotJob($this->user);
    $job->handle();

    // Assert that a snapshot was created for today
    $snapshot = UserPortfolioSnapshot::withoutGlobalScopes()
        ->where('user_id', $this->user->id)
        ->whereDate('aggregate_date', now())
        ->first();

    expect($snapshot)->not->toBeNull()
        ->and($snapshot->balance_cents)->toBe(0);

    // Assert that ProcessSnapshotBalancesJob was dispatched with the correct snapshot and user
    Queue::assertPushed(ProcessSnapshotBalancesJob::class, fn ($job) => $job->snapshot->id === $snapshot->id && $job->user->id === $this->user->id);
});

it('handles case when user is null', function () {
    Queue::fake();

    // Create and dispatch the job with null user
    $job = new ProcessSnapshotJob(null);
    $job->handle();

    // Assert that no snapshot was created
    expect(UserPortfolioSnapshot::withoutGlobalScopes()->count())->toBe(0);

    // Assert that no balance calculation job was dispatched
    Queue::assertNotPushed(ProcessSnapshotBalancesJob::class);
});

it('updates existing snapshot instead of creating new one', function () {
    Queue::fake();

    // Create an existing snapshot for today
    $existingSnapshot = UserPortfolioSnapshot::create([
        'user_id' => $this->user->id,
        'aggregate_date' => now()->toDateString(),
        'balance_cents' => 1000,
        'change' => 100,
    ]);

    // Create and dispatch the job
    $job = new ProcessSnapshotJob($this->user);
    $job->handle();

    // Assert that the existing snapshot was updated
    expect($existingSnapshot->fresh()->balance_cents)->toBe(1000);

    // Assert that ProcessSnapshotBalancesJob was dispatched with the existing snapshot
    Queue::assertPushed(ProcessSnapshotBalancesJob::class, fn ($job) => $job->snapshot->id === $existingSnapshot->id && $job->user->id === $this->user->id);
});
