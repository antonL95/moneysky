<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\UserKrakenAccount;
use App\Services\CryptoExchangeService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessKrakenAccountsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserKrakenAccount $krakenAccount,
    ) {}

    public function handle(
        CryptoExchangeService $krakenService,
    ): void {
        $krakenService->saveBalances($this->krakenAccount);
        $user = $this->krakenAccount->user;

        if ($user === null) {
            return;
        }

        ProcessSnapshotJob::dispatch($user);
    }
}
