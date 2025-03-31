<?php

declare(strict_types=1);

use App\Console\Commands\ProcessBankAccountsCommand;
use App\Enums\BankAccountStatus;
use App\Jobs\ProcessBankAccountsJob;
use App\Models\User;
use App\Models\UserBankAccount;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('processes bank accounts for subscribed users', function () {
    // Create a subscribed user with a bank account
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $user->id,
        'status' => BankAccountStatus::READY,
        'access_expires_at' => CarbonImmutable::now()->addDays(30),
    ]);

    // Create a demo user with a bank account (should not be processed)
    $demoUser = User::factory()->create([
        'demo' => true,
    ]);

    Subscription::factory()->create([
        'user_id' => $demoUser->id,
    ]);

    $demoBankAccount = UserBankAccount::factory()->create([
        'user_id' => $demoUser->id,
        'status' => BankAccountStatus::READY,
        'access_expires_at' => CarbonImmutable::now()->addDays(30),
    ]);

    // Create an unsubscribed user with a bank account (should not be processed)
    $unsubscribedUser = User::factory()->create([
        'demo' => false,
    ]);

    $unsubscribedBankAccount = UserBankAccount::factory()->create([
        'user_id' => $unsubscribedUser->id,
        'status' => BankAccountStatus::READY,
        'access_expires_at' => CarbonImmutable::now()->addDays(30),
    ]);

    // Run the command
    artisan(ProcessBankAccountsCommand::class)
        ->assertExitCode(0);

    // Assert that only the subscribed user's bank account was processed
    Queue::assertPushed(ProcessBankAccountsJob::class, fn ($job) => $job->userBankAccount->id === $bankAccount->id);

    Queue::assertNotPushed(ProcessBankAccountsJob::class, fn ($job) => $job->userBankAccount->id === $demoBankAccount->id);

    Queue::assertNotPushed(ProcessBankAccountsJob::class, fn ($job) => $job->userBankAccount->id === $unsubscribedBankAccount->id);
});

it('marks expired bank accounts as expired', function () {
    // Create a subscribed user with an expired bank account
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $user->id,
        'status' => BankAccountStatus::READY,
        'access_expires_at' => CarbonImmutable::now()->subDays(1),
    ]);

    // Run the command
    artisan(ProcessBankAccountsCommand::class)
        ->assertExitCode(0);

    // Assert that the bank account was marked as expired
    $bankAccount->refresh();
    expect($bankAccount->status)->toBe(BankAccountStatus::EXPIRED);

    // Assert that no job was dispatched for the expired account
    Queue::assertNotPushed(ProcessBankAccountsJob::class, fn ($job) => $job->userBankAccount->id === $bankAccount->id);
});

it('processes bank accounts with custom date range', function () {
    // Create a subscribed user with a bank account
    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $user->id,
        'status' => BankAccountStatus::READY,
        'access_expires_at' => CarbonImmutable::now()->addDays(30),
    ]);

    $from = CarbonImmutable::now()->subDays(7);
    $to = CarbonImmutable::now();

    // Run the command with custom date range
    artisan(ProcessBankAccountsCommand::class, [
        'from' => $from->format('Y-m-d'),
        'to' => $to->format('Y-m-d'),
    ])->assertExitCode(0);

    // Assert that the job was dispatched with the correct date range
    Queue::assertPushed(ProcessBankAccountsJob::class, fn ($job) => $job->userBankAccount->id === $bankAccount->id
            && $job->from->format('Y-m-d') === $from->format('Y-m-d')
            && $job->to->format('Y-m-d') === $to->format('Y-m-d'));
});
