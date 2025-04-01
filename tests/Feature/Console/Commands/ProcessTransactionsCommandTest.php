<?php

declare(strict_types=1);

use App\Console\Commands\ProcessTransactionsCommand;
use App\Jobs\ProcessTransactionsJob;
use App\Models\UserBankTransactionRaw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('processes unprocessed transactions', function () {
    // Create some unprocessed transactions
    $unprocessedTransactions = UserBankTransactionRaw::factory()->count(3)->create([
        'processed' => false,
    ]);

    // Create some processed transactions (should not be processed)
    UserBankTransactionRaw::factory()->count(2)->create([
        'processed' => true,
    ]);

    // Run the command
    artisan(ProcessTransactionsCommand::class)
        ->assertExitCode(0);

    // Assert that ProcessTransactionsJob was dispatched with the unprocessed transactions
    Queue::assertPushed(ProcessTransactionsJob::class, fn ($job) => $job->rawTransactionsToProcess->pluck('id')->toArray() === $unprocessedTransactions->pluck('id')->toArray());
});

it('limits the number of transactions processed to 20', function () {
    // Create 25 unprocessed transactions
    UserBankTransactionRaw::factory()->count(25)->create([
        'processed' => false,
    ]);

    // Run the command
    artisan(ProcessTransactionsCommand::class)
        ->assertExitCode(0);

    // Assert that ProcessTransactionsJob was dispatched with only 20 transactions
    Queue::assertPushed(ProcessTransactionsJob::class, fn ($job) => $job->rawTransactionsToProcess->count() === 20);
});

it('does not dispatch job when there are no unprocessed transactions', function () {
    // Create only processed transactions
    UserBankTransactionRaw::factory()->count(3)->create([
        'processed' => true,
    ]);

    // Run the command
    artisan(ProcessTransactionsCommand::class)
        ->assertExitCode(0);

    // Assert that no job was dispatched
    Queue::assertNotPushed(ProcessTransactionsJob::class);
});
