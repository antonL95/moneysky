<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\CalculateBudget;
use App\Models\UserBudgetPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CalculateBudgetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly UserBudgetPeriod $userBudgetPeriod) {}

    public function handle(CalculateBudget $calculateBudget): void
    {
        $calculateBudget->handle($this->userBudgetPeriod);
    }
}
