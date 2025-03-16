<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserBudget;
use App\Models\UserBudgetPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class UserBudgetPeriodFactory extends Factory
{
    protected $model = UserBudgetPeriod::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'balance_cents' => $this->faker->randomNumber(),

            'user_budget_id' => UserBudget::factory(),
        ];
    }
}
