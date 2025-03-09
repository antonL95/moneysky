<?php

declare(strict_types=1);

namespace App\Actions\KrakenAccount;

use App\Data\KrakenAccountData;
use App\Jobs\ProcessKrakenAccountsJob;
use App\Models\User;

final readonly class CreateKrakenAccount
{
    public function handle(User $user, KrakenAccountData $data): void
    {
        $userKrakenAccount = $user->userKrakenAccount()->create([
            'private_key' => $data->privateKey,
            'api_key' => $data->apiKey,
        ]);

        ProcessKrakenAccountsJob::dispatch($userKrakenAccount);
    }
}
