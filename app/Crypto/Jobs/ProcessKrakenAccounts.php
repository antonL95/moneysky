<?php

declare(strict_types=1);

namespace App\Crypto\Jobs;

use App\Crypto\Models\UserKrakenAccount;
use App\Crypto\Services\KrakenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessKrakenAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserKrakenAccount $krakenAccount,
    ) {
    }

    public function handle(
        KrakenService $krakenService,
    ): void {
        $krakenService->saveBalances($this->krakenAccount);
    }
}
