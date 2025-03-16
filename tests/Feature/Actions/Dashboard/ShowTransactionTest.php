<?php

use App\Actions\Dashboard\ShowTransaction;
use App\Jobs\RecalculateTransactionAggregatesJob;
use App\Models\UserTransaction;
use function Pest\Laravel\actingAs;

it('successfully shows transaction', function () {
    Queue::fake();
    $user = \App\Models\User::factory()->create(['demo' => false]);
    $transaction = UserTransaction::factory()->create(
        ['hidden' => true, 'user_id' => $user->id],
    );

    actingAs($user);
    $action = app(ShowTransaction::class);

    $action->handle($user, $transaction);

    expect($transaction->fresh()->hidden)->toBeFalse();

    Queue::assertPushed(RecalculateTransactionAggregatesJob::class);
});
