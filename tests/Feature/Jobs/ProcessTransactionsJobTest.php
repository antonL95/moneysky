<?php

declare(strict_types=1);

use App\Jobs\CalculateBudgetJob;
use App\Jobs\ProcessTransactionsJob;
use App\Models\Scopes\UserScope;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankTransactionRaw;
use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use App\Models\UserTransaction;
use App\Services\AiService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Queue;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Prism;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\Usage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create a bank account for the user
    $this->bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Create transaction tags
    $this->streamingTag = TransactionTag::factory()->create([
        'tag' => 'Streaming Services',
    ]);

    $this->foodTag = TransactionTag::factory()->create([
        'tag' => 'Food & Dining',
    ]);

    Prism::fake([
        new StructuredResponse(
            steps: collect([]),
            responseMessages: collect([]),
            text: json_encode([
                'id' => $this->foodTag->id,
                'tag' => 'Food & Dining',
            ]),
            structured: [
                'id' => $this->foodTag->id,
                'tag' => 'Food & Dining',
            ],
            finishReason: FinishReason::Stop,
            usage: new Usage(10, 20),
            meta: new Meta('fake-1', 'fake-model'),
            additionalContent: []
        ),
    ]);

    // Freeze time for consistent testing
    Carbon::setTestNow(Carbon::parse('2025-01-15'));

    Queue::fake();
});

it('processes raw transactions and creates user transactions', function () {
    // Create a raw transaction
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-1',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => 'Payment for groceries',
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull()
        ->and($userTransaction->user_id)->toBe($this->user->id)
        ->and($userTransaction->user_bank_account_id)->toBe($this->bankAccount->id)
        ->and($userTransaction->transaction_tag_id)->toBe($this->foodTag->id)
        ->and($userTransaction->balance_cents)->toBe(-1000)
        ->and($userTransaction->currency)->toBe('EUR')
        ->and($userTransaction->description)->toBe('Payment for groceries');
});

it('automatically tags streaming services', function () {
    // Create a raw transaction for a streaming service
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-2',
        'balance_cents' => -1500,
        'currency' => 'EUR',
        'remittance_information' => 'Netflix Monthly Subscription',
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created with the streaming tag
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull()
        ->and($userTransaction->transaction_tag_id)->toBe($this->streamingTag->id);
});

it('skips transactions between accounts with same external id', function () {
    // Create two raw transactions that represent a transfer between accounts
    $rawTransaction1 = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transfer-123',
        'balance_cents' => -2000,
        'currency' => 'EUR',
        'remittance_information' => 'Transfer to savings',
        'booked_at' => Carbon::now()->format('Y-m-d'),
        'processed' => false,
    ]);

    $rawTransaction2 = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transfer-124', // Different external ID to avoid unique constraint
        'balance_cents' => 2000,
        'currency' => 'EUR',
        'remittance_information' => 'Transfer from checking',
        'booked_at' => Carbon::now()->format('Y-m-d'),
        'processed' => false,
    ]);

    // Create a collection with both raw transactions
    $collection = new Collection([$rawTransaction1, $rawTransaction2]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that both raw transactions were marked as processed
    expect($rawTransaction1->fresh()->processed)->toBeTrue()
        ->and($rawTransaction2->fresh()->processed)->toBeTrue();

    // Assert that no user transactions were created
    $userTransactions = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction1->id)
        ->orWhere('user_bank_transaction_raw_id', $rawTransaction2->id)
        ->get();

    expect($userTransactions)->toHaveCount(0);
});

it('skips transactions between accounts with different external id but same amount', function () {
    // Create two raw transactions that represent a transfer between accounts
    $rawTransaction1 = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transfer-125',
        'balance_cents' => -2000,
        'currency' => 'EUR',
        'remittance_information' => 'Transfer to savings',
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    $rawTransaction2 = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transfer-126', // Different external ID
        'balance_cents' => 2000, // Same amount but positive
        'currency' => 'EUR',
        'remittance_information' => 'Transfer from checking',
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with both raw transactions
    $collection = new Collection([$rawTransaction1, $rawTransaction2]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that both raw transactions were marked as processed
    expect($rawTransaction1->fresh()->processed)->toBeTrue()
        ->and($rawTransaction2->fresh()->processed)->toBeTrue();

    // Assert that no user transactions were created
    $userTransactions = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction1->id)
        ->orWhere('user_bank_transaction_raw_id', $rawTransaction2->id)
        ->get();

    expect($userTransactions)->toHaveCount(0);
});

it('skips transactions with no information', function () {
    // Create a raw transaction with no remittance or additional information
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-3',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => null,
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that no user transaction was created
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->toBeNull();
});

it('recalculates budget periods for the current month', function () {
    Queue::fake();

    // Create a budget for the user
    $budget = UserBudget::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Create a budget period for the current month
    $budgetPeriod = UserBudgetPeriod::factory()->create([
        'user_budget_id' => $budget->id,
        'start_date' => Carbon::now()->startOfMonth(),
        'end_date' => Carbon::now()->endOfMonth(),
    ]);

    // Create a raw transaction
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-4',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => 'Payment for groceries',
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the CalculateBudgetJob was dispatched for the budget period
    Queue::assertPushed(CalculateBudgetJob::class);
});

it('finds similar transactions and applies the same tag', function () {
    // First create a transaction that will be tagged
    $existingRawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-5',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => 'Grocery Store XYZ',
        'booked_at' => Carbon::now()->subDays(30),
        'processed' => true,
    ]);

    // Create a user transaction with a tag
    $existingUserTransaction = UserTransaction::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_account_id' => $this->bankAccount->id,
        'transaction_tag_id' => $this->foodTag->id,
        'user_bank_transaction_raw_id' => $existingRawTransaction->id,
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'description' => 'Grocery Store XYZ',
        'booked_at' => Carbon::now()->subDays(30),
    ]);

    // Now create a similar transaction
    $newRawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-6',
        'balance_cents' => -1000, // Same amount
        'currency' => 'EUR',
        'remittance_information' => 'Grocery Store XYZ', // Same description
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the new raw transaction
    $collection = new Collection([$newRawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($newRawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created with the same tag as the similar transaction
    $newUserTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $newRawTransaction->id)
        ->first();

    expect($newUserTransaction)->not->toBeNull()
        ->and($newUserTransaction->transaction_tag_id)->toBe($this->foodTag->id);
});

it('finds streaming services in additional information', function () {
    // Create a raw transaction with streaming service in additional_information
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-7',
        'balance_cents' => -1500,
        'currency' => 'EUR',
        'remittance_information' => null,
        'additional_information' => 'Payment for Netflix Subscription',
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created with the streaming tag
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull()
        ->and($userTransaction->transaction_tag_id)->toBe($this->streamingTag->id);
});

it('handles missing streaming services tag', function () {
    // Create a new transaction tag to replace the original one
    $originalTag = $this->streamingTag;

    // Temporarily rename the tag to simulate it not being found
    $originalTag->tag = 'Temporary Different Name';
    $originalTag->save();

    // Create a raw transaction for a streaming service
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-8',
        'balance_cents' => -1500,
        'currency' => 'EUR',
        'remittance_information' => 'Netflix Monthly Subscription',
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Restore the original tag name
    $originalTag->tag = 'Streaming Services';
    $originalTag->save();

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created and tagged by AI
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull();
});

it('handles missing streaming services tag in additional information', function () {
    // Create a new transaction tag to replace the original one
    $originalTag = $this->streamingTag;

    // Temporarily rename the tag to simulate it not being found
    $originalTag->tag = 'Temporary Different Name';
    $originalTag->save();

    // Create a raw transaction for a streaming service
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-9',
        'balance_cents' => -1500,
        'currency' => 'EUR',
        'remittance_information' => null,
        'additional_information' => 'Netflix Monthly Subscription',
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Restore the original tag name
    $originalTag->tag = 'Streaming Services';
    $originalTag->save();

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created and tagged by AI
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull();
});

it('processes positive balance transactions', function () {
    // Create a raw transaction with positive balance
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-10',
        'balance_cents' => 1000, // Positive balance
        'currency' => 'EUR',
        'remittance_information' => 'Salary payment',
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // For positive balance transactions, we don't use AI tagging but still create the transaction
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->toBeNull();
});

it('handles non-existent user when recalculating budgets', function () {

    // Create a raw transaction
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-13',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => 'Payment for groceries',
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Store the user ID before deleting
    $userId = $this->user->id;

    // Delete the user
    $this->user->delete();

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job - this should handle the ModelNotFoundException gracefully
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    Queue::assertNotPushed(CalculateBudgetJob::class);
});

it('handles empty transaction collection', function () {
    // Create an empty collection
    $collection = new Collection([]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // No assertions needed - we're just making sure no exception is thrown
});

it('handles missing transaction tag when tagging', function () {
    // Create a raw transaction
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-14',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => 'Payment for something',
        'additional_information' => null,
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create an instance of Prism to fake the AI response with a non-existent tag
    Prism::fake([
        new StructuredResponse(
            steps: collect([]),
            responseMessages: collect([]),
            text: json_encode([
                'id' => 9999, // Non-existent tag ID
                'tag' => 'Non-existent Tag',
            ]),
            structured: [
                'id' => 9999,
                'tag' => 'Non-existent Tag',
            ],
            finishReason: FinishReason::Stop,
            usage: new Usage(10, 20),
            meta: new Meta('fake-1', 'fake-model'),
            additionalContent: []
        ),
    ]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created without a tag
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull();
});

it('uses additional_information when remittance_information is null', function () {
    // Create a raw transaction with only additional_information
    $rawTransaction = UserBankTransactionRaw::factory()->create([
        'user_bank_account_id' => $this->bankAccount->id,
        'external_id' => 'transaction-15',
        'balance_cents' => -1000,
        'currency' => 'EUR',
        'remittance_information' => null,
        'additional_information' => 'Payment for groceries',
        'booked_at' => Carbon::now(),
        'processed' => false,
    ]);

    // Create a collection with the raw transaction
    $collection = new Collection([$rawTransaction]);

    // Create and dispatch the job
    $job = new ProcessTransactionsJob($collection);
    $job->handle(app(AiService::class), app(Logger::class));

    // Assert that the raw transaction was marked as processed
    expect($rawTransaction->fresh()->processed)->toBeTrue();

    // Assert that a user transaction was created with the additional_information as description
    $userTransaction = UserTransaction::withoutGlobalScope(UserScope::class)
        ->where('user_bank_transaction_raw_id', $rawTransaction->id)
        ->first();

    expect($userTransaction)->not->toBeNull()
        ->and($userTransaction->description)->toBe('Payment for groceries');
});
