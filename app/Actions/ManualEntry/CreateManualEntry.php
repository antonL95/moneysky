<?php

declare(strict_types=1);

namespace App\Actions\ManualEntry;

use App\Data\App\ManualEntry\ManualEntryData;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;

final readonly class CreateManualEntry
{
    public function handle(User $user, ManualEntryData $data): void
    {
        $user->userManualEntry()->create([
            'name' => $data->name,
            'description' => $data->description,
            'balance_cents' => (int) ($data->balance * 100),
            'currency' => $data->currency,
        ]);

        ProcessSnapshotJob::dispatch($user);
    }
}
