<?php

declare(strict_types=1);

namespace App\Console;

use App\Jobs\ProcessBankAccounts;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\Models\UserBankAccount;
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

        $userBankAccounts = [];

        $users = User::where('demo', '=', false)->get();

        foreach ($users as $user) {
            UserBankAccount::withoutGlobalScope(
                UserScope::class,
            )->where('user_id', $user->id)->each(
                function (UserBankAccount $bankAccount) use (&$userBankAccounts) {
                    $userBankAccounts[] = $bankAccount;
                },
            );
        }

        $i = 0;
        foreach ($userBankAccounts as $userBankAccount) {
            ProcessBankAccounts::dispatch($userBankAccount, $from, $to)
                ->delay(now()->addSeconds($i * 5));
            ++$i;
        }

        return 0;
    }
}
