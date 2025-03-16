<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BankAccountStatus;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBankAccount>
 */
final class UserBankAccountFactory extends Factory
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
            'currency' => 'USD',
            'iban' => $this->faker->iban('CZ'),
            'access_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => BankAccountStatus::READY->value,
        ];
    }
}
