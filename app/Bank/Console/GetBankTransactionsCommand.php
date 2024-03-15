<?php

declare(strict_types=1);

namespace App\Bank\Console;

use App\Bank\Enums\Status;
use App\Bank\Jobs\ProcessBankAccounts;
use App\Bank\Models\UserBankAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetBankTransactionsCommand extends Command
{
    protected $signature = 'app:get-bank-transactions {from?} {to?}';

    protected $description = 'Download transactions and current balance from bank';

    public function handle(): int
    {
        $fromArg = $this->argument('from');
        if (\is_string($fromArg)) {
            $from = Carbon::createFromFormat('Y-m-d', $fromArg);
        } else {
            $from = Carbon::now()
                ->setTime(0, 0)
                ->subDays();
        }

        $toArg = $this->argument('to');

        if (\is_string($toArg)) {
            $to = Carbon::createFromFormat('Y-m-d', $toArg);
        } else {
            $to = Carbon::now()
                ->setTime(0, 0);
        }

        $accounts = UserBankAccount::whereStatus(Status::LINKED->value)
            ->withoutGlobalScopes()
            ->get();

        $i = 0;
        foreach ($accounts as $account) {
            ProcessBankAccounts::dispatch($account, $from, $to)
                ->delay(now()->addSeconds($i * 5));
            ++$i;
        }

        return 0;
    }
}
