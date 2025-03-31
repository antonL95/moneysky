<?php

declare(strict_types=1);

use App\Actions\TransactionAggregate\CalculateTransactionAggregation;
use App\Enums\CacheKeys;
use App\Jobs\RecalculateTransactionAggregatesJob;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserTransaction;
use App\Models\UserTransactionAggregate;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
    ]);
    $this->tag = TransactionTag::factory()->create([
        'tag' => 'Food & Dining',
    ]);

    $this->transaction = UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_account_id' => $this->bankAccount->id,
        'transaction_tag_id' => $this->tag->id,
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'booked_at' => CarbonImmutable::now(),
    ]);

    Cache::flush();
});

it('creates a new transaction aggregate when none exists', function () {
    $job = new RecalculateTransactionAggregatesJob(
        $this->user,
        $this->transaction
    );

    $job->handle(app(CalculateTransactionAggregation::class));

    actingAs($this->user);

    // Assert that a transaction aggregate was created
    $aggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->tag->id)
        ->whereDate('aggregate_date', CarbonImmutable::now()->toDateString())
        ->first();

    expect($aggregate)->not->toBeNull()
        ->and(abs($aggregate->balance_cents))->toBe(1000);
});

it('updates an existing transaction aggregate', function () {
    // Create an existing aggregate
    $existingAggregate = UserTransactionAggregate::create([
        'user_id' => $this->user->id,
        'transaction_tag_id' => $this->tag->id,
        'aggregate_date' => CarbonImmutable::now(),
        'balance_cents' => 500,
        'change' => 0,
    ]);

    actingAs($this->user);

    // Create the job
    $job = new RecalculateTransactionAggregatesJob(
        $this->user,
        $this->transaction
    );

    // Run the job
    $job->handle(app(CalculateTransactionAggregation::class));

    // Refresh the aggregate from the database
    $existingAggregate->refresh();

    // Assert that the aggregate was updated
    expect(abs($existingAggregate->balance_cents))->toBe(1000);
});

it('handles transactions with no tag', function () {
    // Create a transaction without a tag
    $transactionWithoutTag = UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_account_id' => $this->bankAccount->id,
        'transaction_tag_id' => null,
        'balance_cents' => -2000,
        'currency' => 'EUR',
        'booked_at' => CarbonImmutable::now(),
    ]);

    actingAs($this->user);
    // Create the job
    $job = new RecalculateTransactionAggregatesJob(
        $this->user,
        $transactionWithoutTag
    );

    // Run the job
    $job->handle(app(CalculateTransactionAggregation::class));

    // Assert that a transaction aggregate was created with null tag_id
    $aggregate = UserTransactionAggregate::whereNull('transaction_tag_id')
        ->whereDate('aggregate_date', CarbonImmutable::now()->toDateString())
        ->first();

    expect($aggregate)->not->toBeNull()
        ->and(abs($aggregate->balance_cents))->toBe(2000);
});

it('clears the cache after updating aggregates', function () {
    // Set up a cache key to test
    $cacheKey = sprintf(
        CacheKeys::TRANSACTION_AGGREGATE->value,
        $this->user->id,
        CarbonImmutable::now()->format('m-Y')
    );

    // Put something in the cache
    Cache::put($cacheKey, 'test-value', 60);

    // Verify the cache has our value
    expect(Cache::has($cacheKey))->toBeTrue();

    // Create the job
    $job = new RecalculateTransactionAggregatesJob(
        $this->user,
        $this->transaction
    );

    // Run the job
    $job->handle(app(CalculateTransactionAggregation::class));

    // Assert that the cache was cleared
    expect(Cache::has($cacheKey))->toBeFalse();
});

it('passes the correct date from the transaction to the action', function () {
    // Create a transaction with a specific date
    $specificDate = CarbonImmutable::parse('2025-02-20');
    $transactionWithSpecificDate = UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_account_id' => $this->bankAccount->id,
        'transaction_tag_id' => $this->tag->id,
        'balance_cents' => -3000,
        'currency' => 'EUR',
        'booked_at' => $specificDate,
    ]);

    actingAs($this->user);

    // Create the job
    $job = new RecalculateTransactionAggregatesJob(
        $this->user,
        $transactionWithSpecificDate
    );

    // Run the job
    $job->handle(app(CalculateTransactionAggregation::class));

    // Assert that a transaction aggregate was created with the specific date
    $aggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->tag->id)
        ->whereDate('aggregate_date', $specificDate->toDateString())
        ->first();

    expect($aggregate)->not->toBeNull()
        ->and(abs($aggregate->balance_cents))->toBe(3000);
});
