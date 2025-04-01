<?php

declare(strict_types=1);

use App\Jobs\RecalculateTransactionAggregatesJob;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

beforeEach(function () {
    Queue::fake();
    $this->user = User::factory()->create([
        'demo' => false,
    ]);
});

it('can hide transaction with authorization check', function () {
    $transaction = UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'hidden' => false,
    ]);

    actingAs($this->user);

    put(route('spending.transaction.hide', ['transaction' => $transaction->id]))
        ->assertSessionHas('flash');

    expect($transaction->fresh()->hidden)->toBeTrue();
    Queue::assertPushed(RecalculateTransactionAggregatesJob::class);
});

it('cant hide transaction of another user', function () {
    $user2 = User::factory()->create([
        'demo' => false,
    ]);

    $transaction = UserTransaction::factory()->create([
        'user_id' => $user2->id,
        'hidden' => false,
    ]);

    actingAs($this->user);

    put(route('spending.transaction.hide', ['transaction' => $transaction->id]))
        ->assertStatus(404);

    expect($transaction->fresh()->hidden)->toBeFalse();
    Queue::assertNotPushed(RecalculateTransactionAggregatesJob::class);
});

it('cant hide transaction when not authenticated', function () {
    $transaction = UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'hidden' => false,
    ]);

    put(route('spending.transaction.hide', ['transaction' => $transaction->id]))
        ->assertRedirect(route('login'));

    expect($transaction->fresh()->hidden)->toBeFalse();
    Queue::assertNotPushed(RecalculateTransactionAggregatesJob::class);
});
