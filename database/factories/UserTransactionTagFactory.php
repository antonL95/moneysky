<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserTransactionTag;
use Illuminate\Database\Eloquent\Factories\Factory;

final class UserTransactionTagFactory extends Factory
{
    protected $model = UserTransactionTag::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'tag' => $this->faker->word(),
            'color' => $this->faker->hexColor(),
        ];
    }
}
