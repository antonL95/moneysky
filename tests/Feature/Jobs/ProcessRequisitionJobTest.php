<?php

declare(strict_types=1);

use App\Jobs\ProcessRequisitionJob;
use App\Models\User;
use App\Models\UserBankSession;
use App\Services\BankService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create a bank session
    $this->bankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'requisition_id' => 'test-requisition-id',
    ]);
});

it('deletes the requisition and bank session', function () {
    // Create and dispatch the job
    $job = new ProcessRequisitionJob($this->bankSession);
    $job->handle(app(BankService::class));

    // Assert that the bank session was deleted
    expect(UserBankSession::withoutGlobalScopes()->find($this->bankSession->id))->toBeNull();
});

it('handles case when bank session is already deleted', function () {
    // Delete the bank session first
    $this->bankSession->delete();

    // Create and dispatch the job
    $job = new ProcessRequisitionJob($this->bankSession);
    $job->handle(app(BankService::class));

    // Assert that the bank session remains deleted
    expect(UserBankSession::withoutGlobalScopes()->find($this->bankSession->id))->toBeNull();
});
