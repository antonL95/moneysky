<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserStockMarket;
use Illuminate\Database\Eloquent\Factories\Factory;

final class UserStockMarketFactory extends Factory
{
    protected $model = UserStockMarket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'ticker' => $this->faker->company,
            'amount' => $this->faker->numberBetween(1, 100),
        ];
    }
}
