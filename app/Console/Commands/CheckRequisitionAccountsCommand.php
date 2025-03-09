<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessRequisitionJob;
use App\Models\UserBankSession;
use Illuminate\Console\Command;

final class CheckRequisitionAccountsCommand extends Command
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
            ProcessRequisitionJob::dispatch($requisition);
        }
    }
}
