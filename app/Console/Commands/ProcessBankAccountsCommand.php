<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BankAccountStatus;
use App\Jobs\ProcessBankAccountsJob;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\Models\UserBankAccount;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class ProcessBankAccountsCommand extends Command
{
    protected $signature = 'app:process-bank-accounts {from?} {to?}';

    protected $description = 'Process raw bank transactions and tag them with categories';

    public function handle(): void
    {
        $users = User::with('subscriptions')
            ->where('demo', '=', false)->get();

        $now = CarbonImmutable::now();

        $from = $this->argument('from') !== null
            ? CarbonImmutable::parse($this->argument('from'))
            : $now->startOfDay();

        $to = $this->argument('to') !== null
            ? CarbonImmutable::parse($this->argument('to'))
            : $now;

        /** @var User $user */
        foreach ($users as $user) {
            if (! $user->subscribed()) {
                continue;
            }

            UserBankAccount::withoutGlobalScope(
                UserScope::class,
            )->where('user_id', $user->id)->each(
                function (UserBankAccount $bankAccount) use ($from, $to): void {
                    if ($bankAccount->access_expires_at < now()) {
                        $bankAccount->status = BankAccountStatus::EXPIRED;
                        $bankAccount->save();

                        return;
                    }

                    ProcessBankAccountsJob::dispatch($bankAccount, $from, $to);
                },
            );
        }
    }
}
