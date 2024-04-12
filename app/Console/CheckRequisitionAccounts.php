<?php

declare(strict_types=1);

namespace App\Console;

use App\Jobs\ProcessRequisition;
use App\Models\UserBankSession;
use Illuminate\Console\Command;

class CheckRequisitionAccounts extends Command
{
    protected $signature = 'app:check-requisition-accounts';

    protected $description = 'Process all requisitions that do not have bank account assigned.';

    public function handle(): void
    {

        $requisitions = UserBankSession::withoutGlobalScopes()
            ->getQuery()
            ->leftJoin('user_bank_accounts', 'user_bank_sessions.id', '=', 'user_bank_accounts.user_bank_session_id')
            ->whereNull('user_bank_accounts.id')
            ->get();

        /** @var UserBankSession $requisition */
        foreach ($requisitions as $requisition) {
            ProcessRequisition::dispatch($requisition);
        }
    }
}
