<?php

declare(strict_types=1);

namespace App\Actions\ManualEntry;

use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserManualEntry;

final readonly class DeleteManualEntry
{
    public function handle(UserManualEntry $manualEntry): void
    {
        /** @var User $user */
        $user = $manualEntry->user;
        $manualEntry->delete();

        ProcessSnapshotJob::dispatch($user);
    }
}
