<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPortfolioSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class UserPortfolioSnapshotFactory extends Factory
{
    protected $model = UserPortfolioSnapshot::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance_cents' => $this->faker->randomNumber(),
            'change' => 0,
            'aggregate_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
