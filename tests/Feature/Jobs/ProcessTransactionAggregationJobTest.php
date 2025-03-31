<?php

declare(strict_types=1);

use App\Actions\TransactionAggregate\CreateTransactionAggregation;
use App\Jobs\ProcessTransactionAggregationJob;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserTransactionAggregate;
use Carbon\CarbonImmutable;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create transaction tags
    $this->foodTag = TransactionTag::factory()->create([
        'tag' => 'Food & Dining',
    ]);

    $this->transportTag = TransactionTag::factory()->create([
        'tag' => 'Transportation',
    ]);

    // Create some transactions for the user
    $this->now = CarbonImmutable::now();

    // Create transactions for today
    UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'transaction_tag_id' => $this->foodTag->id,
        'balance_cents' => -1000, // -10.00
        'currency' => 'EUR',
        'booked_at' => $this->now,
    ]);

    UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'transaction_tag_id' => $this->transportTag->id,
        'balance_cents' => -500, // -5.00
        'currency' => 'EUR',
        'booked_at' => $this->now,
    ]);

    // Create an untagged transaction
    UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'transaction_tag_id' => null,
        'balance_cents' => -2000, // -20.00
        'currency' => 'EUR',
        'booked_at' => $this->now,
    ]);
});

it('processes transaction aggregations for current date', function () {
    // Create and dispatch the job
    $job = new ProcessTransactionAggregationJob($this->user);
    $job->handle(app(CreateTransactionAggregation::class));
    actingAs($this->user);

    // Assert that aggregations were created for each tag and untagged transactions
    $foodAggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->foodTag->id)
        ->whereDate('aggregate_date', $this->now)
        ->first();

    $transportAggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->transportTag->id)
        ->whereDate('aggregate_date', $this->now)
        ->first();

    $untaggedAggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->whereNull('transaction_tag_id')
        ->whereDate('aggregate_date', $this->now)
        ->first();

    expect($foodAggregate)->not->toBeNull()
        ->and($foodAggregate->balance_cents)->toBe(-1000)
        ->and($transportAggregate)->not->toBeNull()
        ->and($transportAggregate->balance_cents)->toBe(-500)
        ->and($untaggedAggregate)->not->toBeNull()
        ->and($untaggedAggregate->balance_cents)->toBe(-2000);
});

it('processes transaction aggregations for a date range', function () {
    // Create transactions for yesterday
    $yesterday = $this->now->subDay();

    UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'transaction_tag_id' => $this->foodTag->id,
        'balance_cents' => -1500, // -15.00
        'currency' => 'EUR',
        'booked_at' => $yesterday,
    ]);
    actingAs($this->user);

    // Create and dispatch the job with a from date
    $job = new ProcessTransactionAggregationJob($this->user, $yesterday);
    $job->handle(app(CreateTransactionAggregation::class));

    // Assert that aggregations were created for both days
    $todayFoodAggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->foodTag->id)
        ->whereDate('aggregate_date', $this->now)
        ->first();

    $yesterdayFoodAggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->foodTag->id)
        ->whereDate('aggregate_date', $yesterday)
        ->first();

    expect($todayFoodAggregate)->not->toBeNull()
        ->and($todayFoodAggregate->balance_cents)->toBe(-1000)
        ->and($yesterdayFoodAggregate)->not->toBeNull()
        ->and($yesterdayFoodAggregate->balance_cents)->toBe(-1500);
});

it('updates existing aggregations instead of creating new ones', function () {
    // Create an existing aggregation
    UserTransactionAggregate::create([
        'user_id' => $this->user->id,
        'transaction_tag_id' => $this->foodTag->id,
        'aggregate_date' => $this->now,
        'balance_cents' => -500, // Old value
        'change' => 0,
    ]);
    actingAs($this->user);

    // Create and dispatch the job
    $job = new ProcessTransactionAggregationJob($this->user);
    $job->handle(app(CreateTransactionAggregation::class));

    // Assert that the existing aggregation was updated
    $aggregate = UserTransactionAggregate::where('user_id', $this->user->id)
        ->where('transaction_tag_id', $this->foodTag->id)
        ->whereDate('aggregate_date', $this->now)
        ->first();

    expect($aggregate)->not->toBeNull()
        ->and($aggregate->balance_cents)->toBe(-1000); // New value
});
