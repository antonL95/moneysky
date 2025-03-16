<?php

use App\Actions\Dashboard\HideTransaction;
use App\Jobs\RecalculateTransactionAggregatesJob;
use App\Models\UserTransaction;
use function Pest\Laravel\actingAs;

it('successfully hides transaction', function () {
    Queue::fake();
    $user = \App\Models\User::factory()->create(['demo' => false]);
    $transaction = UserTransaction::factory()->create(
        ['hidden' => false, 'user_id' => $user->id],
    );

    actingAs($user);
    $action = app(HideTransaction::class);

    $action->handle($user, $transaction);

    expect($transaction->fresh()->hidden)->toBeTrue();

    Queue::assertPushed(RecalculateTransactionAggregatesJob::class);
});
