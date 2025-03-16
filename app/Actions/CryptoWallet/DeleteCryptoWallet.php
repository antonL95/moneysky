<?php

declare(strict_types=1);

namespace App\Actions\CryptoWallet;

use App\Jobs\ProcessSnapshotJob;
use App\Models\UserCryptoWallet;

final readonly class DeleteCryptoWallet
{
    public function handle(UserCryptoWallet $cryptoWallet): void
    {
        $user = $cryptoWallet->user;
        $cryptoWallet->delete();
        ProcessSnapshotJob::dispatch($user);
    }
}
