<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\UserBankSession;
use App\Services\BankService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessRequisitionJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public UserBankSession $userBankSession,
    ) {}

    public function handle(BankService $bankService): void
    {
        $bankService->deleteRequisition(
            $this->userBankSession,
        );
    }
}
