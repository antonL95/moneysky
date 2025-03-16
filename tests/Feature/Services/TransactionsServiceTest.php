<?php

declare(strict_types=1);

use App\Models\TransactionTag;
use App\Services\TransactionsService;
use Carbon\CarbonImmutable;
use Database\Seeders\TestSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(TestSeeder::class);

    $this->user = App\Models\User::find(1);
});

it('returns most recent transaction aggregates', function () {
    $service = app(TransactionsService::class);
    $transactionAggregates = $service->getTransactionAggregates($this->user, null);

    expect(count($transactionAggregates))->toBe(12)->and(abs($transactionAggregates[0]->amount))->toBeGreaterThan(0);
});

it('returns most recent transaction aggregates with invalid date', function () {
    $service = app(TransactionsService::class);
    $transactionAggregates = $service->getTransactionAggregates($this->user, '123123');

    expect(count($transactionAggregates))->toBe(12)->and(abs($transactionAggregates[0]->amount))->toBeGreaterThan(0);
});

it('returns transaction aggregates for specific month', function () {
    $service = app(TransactionsService::class);
    $now = CarbonImmutable::now();

    $transactionAggregates = $service->getTransactionAggregates(
        $this->user,
        $now->subDays(30)->format('m-Y'),
    );
    expect(count($transactionAggregates))->toBe(12)->and(abs($transactionAggregates[0]->amount))->toBeGreaterThan(0);
});

it('returns transaction aggregates zero values when no transaction aggregates not found', function () {
    $service = app(TransactionsService::class);
    $now = CarbonImmutable::now();

    $transactionAggregates = $service->getTransactionAggregates(
        $this->user,
        $now->subDays(1000)->format('m-Y'),
    );
    expect(count($transactionAggregates))->toBe(12)
        ->and(abs($transactionAggregates[0]->amount))->toBe(0);
});

it('returns most recent transactions for specific tag', function () {
    $service = app(TransactionsService::class);
    $transactionTag = TransactionTag::inRandomOrder()->first();

    actingAs($this->user);

    $transactions = $service->getTransactions($transactionTag, null);
    $now = CarbonImmutable::now();
    $beginningOfTheMonth = $now->startOfMonth();
    $dayDiff = (int) abs($now->diffInDays($beginningOfTheMonth)) + 1;

    expect(count($transactions))->toBe($dayDiff * 2)
        ->and(abs($transactions[0]->amount))
        ->toBeGreaterThan(0);
});

it('returns most recent transactions for specific tag with invalid date', function () {
    $service = app(TransactionsService::class);
    $transactionTag = TransactionTag::inRandomOrder()->first();

    actingAs($this->user);

    $transactions = $service->getTransactions($transactionTag, 'asdf');
    $now = CarbonImmutable::now();
    $beginningOfTheMonth = $now->startOfMonth();
    $dayDiff = (int) abs($now->diffInDays($beginningOfTheMonth)) + 1;

    expect(count($transactions))->toBe($dayDiff * 2)
        ->and(abs($transactions[0]->amount))
        ->toBeGreaterThan(0);
});

it('returns transactions for specific tag and month', function () {
    $service = app(TransactionsService::class);
    $transactionTag = TransactionTag::inRandomOrder()->first();

    actingAs($this->user);
    $now = CarbonImmutable::now();

    $daysInMonth = $now->subMonth()->daysInMonth;

    $transactions = $service->getTransactions(
        $transactionTag,
        $now->subMonth()->format('m-Y'),
    );

    expect(count($transactions))->toBe($daysInMonth * 2)
        ->and(abs($transactions[0]->amount))
        ->toBeGreaterThan(0);
});

it('returns transactions for nullable tag', function () {
    $service = app(TransactionsService::class);

    actingAs($this->user);
    $now = CarbonImmutable::now();
    $beginningOfTheMonth = $now->startOfMonth();
    $dayDiff = (int) abs($now->diffInDays($beginningOfTheMonth)) + 1;

    $transactions = $service->getTransactions(
        null,
        null,
    );

    expect(count($transactions))->toBe($dayDiff * 2)
        ->and(abs($transactions[0]->amount))
        ->toBeGreaterThan(0);
});

it('returns historical dates', function () {
    $service = app(TransactionsService::class);

    actingAs($this->user);
    $historicalDates = $service->getHistoricalDates($this->user);

    expect(count($historicalDates))->toBe(3)->and($historicalDates[0])
        ->toBe(CarbonImmutable::now()->format('m-Y'));
});
