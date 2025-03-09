<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserKrakenAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserKrakenAccount>
 */
final class UserKrakenAccountFactory extends Factory
{
    protected $model = UserKrakenAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new()->create()->id,
            'balance_cents' => $this->faker->randomNumber(5),
            'api_key' => $this->faker->text(20),
            'private_key' => $this->faker->text(20),
        ];
    }
}
