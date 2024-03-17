<?php

declare(strict_types=1);

namespace Database\Factories\Bank\Models;

use App\Bank\Models\UserTransactionTag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserTransactionTagFactory extends Factory
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
