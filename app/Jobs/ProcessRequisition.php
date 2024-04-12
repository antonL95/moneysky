<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\UserBankSession;
use App\Services\BankService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRequisition implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserBankSession $userBankSession,
    ) {
    }

    public function handle(BankService $bankService): void
    {
        $bankService->deleteNotUsedRequisition(
            $this->userBankSession,
        );
    }
}
