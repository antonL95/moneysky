<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserTransactionAggregate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class UserTransactionAggregateFactory extends Factory
{
    protected $model = UserTransactionAggregate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'aggregate_date' => Carbon::now(),
            'balance_cents' => $this->faker->randomNumber(),
            'transaction_tag_id' => TransactionTag::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
