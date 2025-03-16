<?php

use App\Actions\ManualEntry\DeleteManualEntry;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserManualEntry;
use function Pest\Laravel\assertDatabaseCount;

it('successfully deletes manual entry', function () {
    Queue::fake();

    $manualEntry = UserManualEntry::factory()->create();

    $action = app(DeleteManualEntry::class);
    assertDatabaseCount('user_manual_entries', 1);
    $action->handle($manualEntry);
    assertDatabaseCount('user_manual_entries', 0);
    Queue::assertPushed(ProcessSnapshotJob::class);
});
