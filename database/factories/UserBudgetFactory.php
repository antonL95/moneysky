<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserBudget;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class UserBudgetFactory extends Factory
{
    protected $model = UserBudget::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
            'balance_cents' => $this->faker->randomNumber(),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'CZK']),
            'user_id' => User::factory(),
        ];
    }
}
