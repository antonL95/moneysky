<?php

declare(strict_types=1);

use App\Services\BudgetsService;
use Carbon\CarbonImmutable;
use Database\Seeders\TestSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(TestSeeder::class);
    $this->user = App\Models\User::find(1);
});

it('returns most recent budgets', function () {
    $service = app(BudgetsService::class);

    actingAs($this->user);
    $budgets = $service->getBudgets($this->user, null);

    expect(count($budgets))->toBe(3)
        ->and($budgets->first()->name)
        ->toBe('Housing')
        ->and($budgets->first()->budget)
        ->toBe(2000.0);
});

it('returns budgets for specific month', function () {
    $service = app(BudgetsService::class);

    actingAs($this->user);
    $budgets = $service->getBudgets(
        $this->user,
        CarbonImmutable::now()->subDays(30)->format('m-Y'),
    );

    expect(count($budgets))->toBe(3)
        ->and($budgets->first()->name)
        ->toBe('Housing')
        ->and($budgets->first()->budget)
        ->toBe(2000.0);
});

it('returns most recent budgets with invalid date provided', function () {
    $service = app(BudgetsService::class);

    actingAs($this->user);
    $budgets = $service->getBudgets($this->user, '123123');

    expect(count($budgets))->toBe(3)
        ->and($budgets->first()->name)
        ->toBe('Housing')
        ->and($budgets->first()->budget)
        ->toBe(2000.0);
});
