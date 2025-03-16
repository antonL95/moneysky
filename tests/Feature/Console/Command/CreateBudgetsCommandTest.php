<?php

use App\Console\Commands\CreateBudgetsCommand;
use App\Models\UserBudget;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

it('successfully creates budget periods', function () {
    UserBudget::factory()->create([]);
    assertDatabaseCount('user_budget_periods', 0);
    artisan(CreateBudgetsCommand::class)->assertOk();
    assertDatabaseCount('user_budget_periods', 1);
});
