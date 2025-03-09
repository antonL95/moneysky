<?php

declare(strict_types=1);

use App\Console\Commands\AggregateTransactionsCommand;
use App\Jobs\ProcessTransactionAggregationJob;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\artisan;

it('calculates the aggregated transactions', function () {
    config(['services.stripe.plus_plan_id' => 'plus']);

    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $queue = Queue::fake();

    artisan(AggregateTransactionsCommand::class)
        ->assertExitCode(0);

    $queue->assertPushed(ProcessTransactionAggregationJob::class, 1);
});
