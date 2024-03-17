<?php

declare(strict_types=1);

namespace Database\Factories\Bank\Models;

use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBankAccount>
 */
class UserBankAccountFactory extends Factory
{
    protected $model = UserBankAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'user_bank_session_id' => UserBankSession::factory()->create(),
            'resource_id' => $this->faker->uuid,
            'external_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'balance_cents' => $this->faker->numberBetween(0, 1000000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'CZK']),
            'iban' => $this->faker->iban('CZ'),
            'access_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}
