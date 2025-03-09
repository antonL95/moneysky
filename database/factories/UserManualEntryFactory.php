<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserManualEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserManualEntry>
 */
final class UserManualEntryFactory extends Factory
{
    protected $model = UserManualEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new()->create()->id,
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'balance_cents' => $this->faker->randomNumber(5),
            'currency' => 'USD',
        ];
    }
}
