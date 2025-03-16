<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BankInstitution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankInstitution>
 */
final class BankInstitutionFactory extends Factory
{
    protected $model = BankInstitution::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->uuid,
            'name' => $this->faker->company,
            'bic' => $this->faker->swiftBicNumber,
            'transaction_total_days' => $this->faker->numberBetween(30, 180),
            'countries' => $this->faker->randomElements(['CZ', 'SK', 'PL', 'HU', 'AT']),
            'logo_url' => $this->faker->imageUrl(),
            'active' => true,
        ];
    }
}
