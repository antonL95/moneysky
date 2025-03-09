<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FeedbackType;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(FeedbackType::cases()),
            'description' => $this->faker->text(),
            'notified' => false,
            'user_id' => User::factory(),
        ];
    }
}
