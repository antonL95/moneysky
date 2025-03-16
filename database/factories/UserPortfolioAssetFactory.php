<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPortfolioAsset;
use App\Models\UserPortfolioSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class UserPortfolioAssetFactory extends Factory
{
    protected $model = UserPortfolioAsset::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'asset_type' => $this->faker->word(),
            'balance_cents' => $this->faker->randomNumber(),
            'user_id' => User::factory(),
            'snapshot_id' => UserPortfolioSnapshot::factory(),
            'change' => 0,
        ];
    }
}
