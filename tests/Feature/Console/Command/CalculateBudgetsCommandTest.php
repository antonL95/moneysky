<?php

declare(strict_types=1);

use App\Console\Commands\CalculateBudgetsCommand;
use App\Jobs\CalculateBudgetJob;
use Database\Seeders\TestSeeder;

use function Pest\Laravel\artisan;
use function Pest\Laravel\seed;

it('pushes budget to the queue', function () {
    Queue::fake();
    seed(TestSeeder::class);

    artisan(CalculateBudgetsCommand::class)
        ->assertExitCode(0);

    Queue::assertPushed(CalculateBudgetJob::class, 3);
});
