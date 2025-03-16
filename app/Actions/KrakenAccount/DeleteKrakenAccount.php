<?php

declare(strict_types=1);

namespace App\Actions\KrakenAccount;

use App\Jobs\ProcessSnapshotJob;
use App\Models\UserKrakenAccount;

final readonly class DeleteKrakenAccount
{
    public function handle(UserKrakenAccount $krakenAccount): void
    {
        $user = $krakenAccount->user;
        if ($user === null) {
            return;
        }

        $krakenAccount->delete();

        ProcessSnapshotJob::dispatch($user);
    }
}
