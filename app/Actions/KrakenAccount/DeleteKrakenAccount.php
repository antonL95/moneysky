<?php

declare(strict_types=1);

namespace App\Actions\KrakenAccount;

use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserKrakenAccount;

final readonly class DeleteKrakenAccount
{
    public function handle(UserKrakenAccount $krakenAccount): void
    {
        /** @var User $user */
        $user = $krakenAccount->user;

        $krakenAccount->delete();

        ProcessSnapshotJob::dispatch($user);
    }
}
