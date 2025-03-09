<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSetting>
 */
final class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new()->create()->id,
            'key' => 'currency',
            'value' => $this->faker->currencyCode,
        ];
    }
}
