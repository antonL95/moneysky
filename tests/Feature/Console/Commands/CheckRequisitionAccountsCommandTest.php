<?php

declare(strict_types=1);

use App\Console\Commands\CheckRequisitionAccountsCommand;
use App\Jobs\ProcessRequisitionJob;
use App\Models\UserBankSession;

use function Pest\Laravel\artisan;

it('pushes to the queue', function () {
    UserBankSession::factory()->create();
    Queue::fake();

    artisan(CheckRequisitionAccountsCommand::class)
        ->assertExitCode(0);

    Queue::assertPushed(ProcessRequisitionJob::class, 1);
});
