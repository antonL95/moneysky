<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChainType;
use App\Models\User;
use App\Models\UserCryptoWallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCryptoWallet>
 */
final class UserCryptoWalletFactory extends Factory
{
    protected $model = UserCryptoWallet::class;

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
