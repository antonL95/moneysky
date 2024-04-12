<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\UserKrakenAccount;
use App\Services\KrakenService;
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
