<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TransactionTag;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TransactionTagFactory extends Factory
{
    protected $model = TransactionTag::class;

    public function definition(): array
    {
        return [
            'tag' => $this->faker->randomElement([
                'Groceries',
                'Dining Out',
                'Utilities',
                'Rent/Mortgage',
                'Streaming Services',
                'Online Subscriptions',
                'Health & Wellness',
                'Shopping',
                'Transportation',
                'Savings & Investments',
            ]),
            'color' => $this->faker->hexColor(),
        ];
    }
}
