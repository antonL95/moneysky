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
            'tag' => $this->faker->word(),
            'color' => $this->faker->hexColor(),
        ];
    }
}
