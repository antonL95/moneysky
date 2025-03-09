<?php

declare(strict_types=1);

namespace App\Actions\ManualEntry;

use App\Jobs\ProcessSnapshotJob;
use App\Models\UserManualEntry;

final readonly class DeleteManualEntry
{
    public function handle(UserManualEntry $manualEntry): void
    {
        $user = $manualEntry->user;
        if ($user === null) {
            return;
        }
        $manualEntry->delete();

        ProcessSnapshotJob::dispatch($user);
    }
}
