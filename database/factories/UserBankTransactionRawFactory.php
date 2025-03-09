<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helpers\CurrencyHelper;
use App\Models\UserBankAccount;
use App\Models\UserBankTransactionRaw;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBankTransactionRaw>
 */
final class UserBankTransactionRawFactory extends Factory
{
    protected $model = UserBankTransactionRaw::class;

    public function definition(): array
    {
        return [
            'balance_cents' => random_int(100, 1000),
            'currency' => CurrencyHelper::defaultCurrency(),
            'external_id' => $this->faker->uuid,
            'booked_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'additional_information' => $this->faker->realText,
            'user_bank_account_id' => UserBankAccount::factory()->create()->id,
        ];
    }
}
