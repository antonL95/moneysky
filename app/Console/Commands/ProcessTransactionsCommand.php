<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessTransactionsJob;
use App\Models\UserBankTransactionRaw;
use Illuminate\Console\Command;

final class ProcessTransactionsCommand extends Command
{
    protected $signature = 'app:process-transactions';

    protected $description = 'Process raw bank transactions and tag them with categories';

    public function handle(): void
    {
        $transactions = UserBankTransactionRaw::whereProcessed(false)
            ->limit(20)
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        ProcessTransactionsJob::dispatch($transactions);
    }
}
