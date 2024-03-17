<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserManualEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserManualEntry>
 */
class UserManualEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new()->create()->id,
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'amount_cents' => $this->faker->randomNumber(5),
            'currency' => $this->faker->currencyCode,
        ];
    }
}
