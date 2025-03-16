<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBankSession>
 */
final class UserBankSessionFactory extends Factory
{
    protected $model = UserBankSession::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'bank_institution_id' => BankInstitution::factory()->create(),
            'link' => $this->faker->url,
            'requisition_id' => $this->faker->uuid,
            'agreement_id' => $this->faker->uuid,
        ];
    }
}
