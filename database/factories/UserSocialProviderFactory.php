<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSocialProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class UserSocialProviderFactory extends Factory
{
    protected $model = UserSocialProvider::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'provider_slug' => $this->faker->slug(),
            'provider_user_id' => $this->faker->word(),
            'nickname' => $this->faker->word(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'avatar' => $this->faker->word(),
            'provider_data' => $this->faker->word(),
            'token' => Str::random(10),
            'refresh_token' => Str::random(10),
            'token_expires_at' => Str::random(10),

            'user_id' => User::factory(),
        ];
    }
}
