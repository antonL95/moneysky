<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChainType;
use App\Models\User;
use App\Models\UserCryptoWallets;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCryptoWallets>
 */
final class UserCryptoWalletsFactory extends Factory
{
    protected $model = UserCryptoWallets::class;

    public function definition(?User $user = null): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'wallet_address' => $this->faker->uuid,
            'chain_type' => $this->faker->randomElement(ChainType::cases()),
            'balance_cents' => $this->faker->randomNumber(5),
        ];
    }
}
