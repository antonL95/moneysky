<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class UserTransactionFactory extends Factory
{
    protected $model = UserTransaction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'user_bank_account_id' => UserBankAccount::factory()->create(),
            'balance_cents' => $this->faker->randomNumber(),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'CZK']),
            'description' => $this->faker->text(),
            'booked_at' => Carbon::now(),
        ];
    }
}
