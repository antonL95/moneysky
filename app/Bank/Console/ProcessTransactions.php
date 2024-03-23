<?php

declare(strict_types=1);

namespace App\Bank\Console;

use App\Bank\Jobs\ProcessTransactionsJob;
use App\Bank\Models\UserBankTransactionRaw;
use Illuminate\Console\Command;

class ProcessTransactions extends Command
{
    protected $signature = 'app:process-transactions';

    protected $description = 'Process raw bank transactions and tag them with categories';

    public function handle(): void
    {
        $transactions = UserBankTransactionRaw::where('processed', '=', false)
            ->limit(20)
            ->get();

        ProcessTransactionsJob::dispatch($transactions);
    }
}
