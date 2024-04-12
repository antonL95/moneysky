<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserTransactionFactory extends Factory
{
    protected $model = UserTransaction::class;

    public function definition(): array
    {
        return [
            'balance_cents' => $this->faker->randomNumber(),
            'currency' => $this->faker->currencyCode(),
            'description' => $this->faker->text(),
            'booked_at' => Carbon::now(),
        ];
    }
}
