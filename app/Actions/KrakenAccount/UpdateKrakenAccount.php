<?php

declare(strict_types=1);

namespace App\Actions\KrakenAccount;

use App\Data\App\KrakenAccount\KrakenAccountData;
use App\Jobs\ProcessKrakenAccountsJob;
use App\Models\UserKrakenAccount;

final readonly class UpdateKrakenAccount
{
    public function handle(UserKrakenAccount $krakenAccount, KrakenAccountData $data): void
    {
        $krakenAccount->update([
            'private_key' => $data->privateKey,
            'api_key' => $data->apiKey,
        ]);
        ProcessKrakenAccountsJob::dispatch($krakenAccount);
    }
}
