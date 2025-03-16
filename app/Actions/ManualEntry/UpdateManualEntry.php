<?php

declare(strict_types=1);

namespace App\Actions\ManualEntry;

use App\Data\App\ManualEntry\ManualEntryData;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserManualEntry;

final readonly class UpdateManualEntry
{
    public function handle(User $user, UserManualEntry $manualEntry, ManualEntryData $data): void
    {
        $manualEntry->update([
            'name' => $data->name,
            'description' => $data->description,
            'balance_cents' => (int) ($data->balance * 100),
            'currency' => $data->currency,
        ]);

        ProcessSnapshotJob::dispatch($user);
    }
}
