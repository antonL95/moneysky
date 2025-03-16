<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessRequisitionJob;
use App\Models\UserBankSession;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

final class CheckRequisitionAccountsCommand extends Command
{
    protected $signature = 'app:check-requisition-accounts';

    protected $description = 'Process all requisitions that do not have bank account assigned.';

    public function handle(): void
    {
        $requisitions = UserBankSession::withoutGlobalScopes()
            ->whereDoesntHave('userBankAccounts', function (Builder $query): void {
                $query->withoutGlobalScopes();
            })
            ->get();

        /** @var UserBankSession $requisition */
        foreach ($requisitions as $requisition) {
            ProcessRequisitionJob::dispatch($requisition);
        }
    }
}
