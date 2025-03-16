<?php

declare(strict_types=1);

use App\Data\App\Dashboard\TransactionData;
use App\Jobs\CalculateBudgetJob;
use App\Jobs\RecalculateTransactionAggregatesJob;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use App\Models\UserManualEntry;
use Carbon\CarbonImmutable;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    Queue::fake();
    $this->user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('can save manual transaction', function () {
    actingAs($this->user);
    post(route('spending.transaction.store'), [
        'balance' => 100,
        'currency' => 'CZK',
        'description' => 'Test transaction',
        'transaction_tag_id' => null,
        'user_manual_entry_id' => null,
    ])->assertSessionHas('flash');

    assertDatabaseCount('user_transactions', 1);
    Queue::assertPushed(RecalculateTransactionAggregatesJob::class);
});

it('can save manual transaction with manual wallet', function () {
    $userManualWallet = UserManualEntry::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 20000,
        'currency' => 'CZK',
        'name' => 'Test Wallet',
    ]);

    actingAs($this->user);

    post(route('spending.transaction.store'), [
        'balance' => 100,
        'currency' => 'CZK',
        'description' => 'Test transaction',
        'user_manual_entry_id' => $userManualWallet->id,
        'transaction_tag_id' => null,
    ]);

    assertDatabaseCount('user_transactions', 1);
    Queue::assertPushed(RecalculateTransactionAggregatesJob::class);

    expect($userManualWallet->fresh()->balance_cents)->toBe(10000);
});

it('can save manual transaction and recalculates budgets', function () {

    $userManualWallet = UserManualEntry::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 20000,
        'currency' => 'CZK',
        'name' => 'Test Wallet',
    ]);

    $userBudget = UserBudget::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 20000,
        'currency' => 'CZK',
    ]);

    UserBudgetPeriod::factory()->create([
        'user_budget_id' => $userBudget->id,
        'start_date' => CarbonImmutable::now()->startOfMonth()->toDateString(),
        'end_date' => CarbonImmutable::now()->endOfMonth()->toDateString(),
        'balance_cents' => 1000,
    ]);

    actingAs($this->user);

    post(route('spending.transaction.store'), [
        'balance' => 100,
        'currency' => 'CZK',
        'description' => 'Test transaction',
        'user_manual_entry_id' => $userManualWallet->id,
        'transaction_tag_id' => null,
    ]);

    assertDatabaseCount('user_transactions', 1);

    Queue::assertPushed(RecalculateTransactionAggregatesJob::class);
    Queue::assertPushed(CalculateBudgetJob::class);

    expect($userManualWallet->fresh()->balance_cents)->toBe(10000);
});

it('can not save manual transaction', function () {
    actingAs($this->user);

    post(route('spending.transaction.store'), [
        'balance' => 100,
        'currency' => 'fake currency',
        'description' => 'Test transaction',
        'user_manual_entry_id' => null,
        'transaction_tag_id' => null,
    ])->assertStatus(302)
        ->assertSessionHasErrors(['currency']);

    assertDatabaseCount('user_transactions', 0);
});

it('can update manual transaction', function () {
    actingAs($this->user);

    $tag = TransactionTag::factory()->create([
        'tag' => 'test',
    ]);

    $transaction = $this->user->userTransaction()->create([
        'transaction_tag_id' => null,
        'user_bank_account_id' => null,
        'user_bank_transaction_raw_id' => null,
        'booked_at' => now(),
        'balance_cents' => 1000_00,
        'currency' => 'EUR',
    ]);

    $data = new TransactionData(
        1200,
        'CZK',
        null,
        $tag->id,
        null,
    );

    put(route('spending.transaction.update', ['user_transaction' => $transaction->id]), $data->toArray())
        ->assertStatus(302)
        ->assertSessionHas('flash');

    expect($transaction->fresh()->balance_cents)
        ->toBe(-1200_00)
        ->and($transaction->fresh()->transaction_tag_id)
        ->toBe($tag->id);
});

it('cant update manual transaction', function () {
    $user = User::factory()->create();
    actingAs($user);

    $tag = TransactionTag::factory()->create([
        'tag' => 'test',
    ]);

    $transaction = $this->user->userTransaction()->create([
        'transaction_tag_id' => null,
        'user_bank_account_id' => null,
        'user_bank_transaction_raw_id' => null,
        'booked_at' => now(),
        'balance_cents' => 1000_00,
        'currency' => 'EUR',
    ]);

    $data = new TransactionData(
        1200,
        'CZK',
        null,
        $tag->id,
        null,
    );

    put(route('spending.transaction.update', ['user_transaction' => $transaction->id]), $data->toArray())
        ->assertStatus(404);

    expect($transaction->fresh()->balance_cents)
        ->toBe(1000_00)
        ->and($transaction->fresh()->transaction_tag_id)
        ->toBeNull();
});

it('can update automatic transaction', function () {
    actingAs($this->user);

    $tag = TransactionTag::factory()->create([
        'tag' => 'test',
    ]);

    $transaction = $this->user->userTransaction()->create([
        'transaction_tag_id' => null,
        'user_bank_account_id' => UserBankAccount::factory()->create(['user_id' => $this->user->id])->id,
        'user_bank_transaction_raw_id' => null,
        'booked_at' => now(),
        'balance_cents' => 1000_00,
        'currency' => 'EUR',
        'description' => 'Test transaction',
    ]);

    $userManualEntry = UserManualEntry::factory()->create(['user_id' => $this->user->id]);

    $data = new TransactionData(
        1200,
        'CZK',
        null,
        $tag->id,
        $userManualEntry->id,
    );

    put(route('spending.transaction.update', ['user_transaction' => $transaction->id]), $data->toArray())
        ->assertStatus(302)
        ->assertSessionHas('flash');

    expect($transaction->fresh()->balance_cents)
        ->toBe(1000_00)
        ->and($transaction->fresh()->transaction_tag_id)
        ->toBe($tag->id)
        ->and($transaction->fresh()->description)
        ->toBe('Test transaction')
        ->and($transaction->fresh()->currency)
        ->toBe('EUR')
        ->and($transaction->fresh()->user_manual_entry_id)
        ->toBeNull();
});

it('cant update automatic transaction', function () {
    $user = User::factory()->create();
    actingAs($user);

    $tag = TransactionTag::factory()->create([
        'tag' => 'test',
    ]);

    $transaction = $this->user->userTransaction()->create([
        'transaction_tag_id' => null,
        'user_bank_account_id' => UserBankAccount::factory()->create(['user_id' => $this->user->id])->id,
        'user_bank_transaction_raw_id' => null,
        'booked_at' => now(),
        'balance_cents' => 1000_00,
        'currency' => 'EUR',
        'description' => 'Test transaction',
    ]);

    $userManualEntry = UserManualEntry::factory()->create(['user_id' => $this->user->id]);

    $data = new TransactionData(
        1200,
        'CZK',
        null,
        $tag->id,
        $userManualEntry->id,
    );

    put(route('spending.transaction.update', ['user_transaction' => $transaction->id]), $data->toArray())
        ->assertStatus(404);

    expect($transaction->fresh()->balance_cents)
        ->toBe(1000_00)
        ->and($transaction->fresh()->transaction_tag_id)
        ->toBeNull()
        ->and($transaction->fresh()->description)
        ->toBe('Test transaction')
        ->and($transaction->fresh()->currency)
        ->toBe('EUR')
        ->and($transaction->fresh()->user_manual_entry_id)
        ->toBeNull();
});

it('can update manual transaction with different currencies', function () {
    actingAs($this->user);

    $tag = TransactionTag::factory()->create([
        'tag' => 'test',
    ]);

    $userManualEntry = UserManualEntry::factory()->create([
        'user_id' => $this->user->id,
        'currency' => 'USD',
        'balance_cents' => 1500_00,
    ]);

    $transaction = $this->user->userTransaction()->create([
        'transaction_tag_id' => null,
        'user_bank_account_id' => null,
        'user_bank_transaction_raw_id' => null,
        'booked_at' => now(),
        'balance_cents' => 1000_00,
        'currency' => 'EUR',
        'user_manual_entry_id' => $userManualEntry->id,
    ]);

    $data = new TransactionData(
        100,
        'EUR',
        null,
        $tag->id,
        $userManualEntry->id,
    );

    put(
        route(
            'spending.transaction.update',
            ['user_transaction' => $transaction->id],
        ),
        $data->toArray(),
    )
        ->assertStatus(302)
        ->assertSessionHas('flash');

    expect($transaction->fresh()->balance_cents)
        ->toBe(-10000)
        ->and($transaction->fresh()->transaction_tag_id)
        ->toBe($tag->id)
        ->and($userManualEntry->fresh()->balance_cents)
        ->toBe(269202);
});
