<?php

declare(strict_types=1);

namespace Database\Factories\MarketData\Models;

use App\MarketData\Models\UserStockMarket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserStockMarketFactory extends Factory
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
