<?php

use App\Actions\Dashboard\UpdateUserBudget;
use App\Data\App\Dashboard\BudgetData;
use App\Models\TransactionTag;
use App\Models\UserBudget;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
});

it('updates budget values without tags', function () {
    $budget = UserBudget::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $action = app(UpdateUserBudget::class);

    $action->handle(
        $budget,
        new BudgetData(
            'Test budget value',
            1234,
            'CZK',
            null,
        ),
    );

    expect($budget->fresh()->name)
        ->toEqual('Test budget value')
        ->and($budget->fresh()->balance_cents)->toEqual(1234_00);
});

it('updates budget values with tags', function () {
    $budget = UserBudget::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $tag = TransactionTag::factory()->create();

    $action = app(UpdateUserBudget::class);

    expect($budget->tags->count())->toEqual(0);

    $action->handle(
        $budget,
        new BudgetData(
            'Test budget value',
            1234,
            'CZK',
            [$tag->id],
        ),
    );

    expect($budget->fresh()->name)
        ->toEqual('Test budget value')
        ->and($budget->fresh()->balance_cents)->toEqual(1234_00)
        ->and($budget->fresh()->tags->count())->toEqual(1);
});
