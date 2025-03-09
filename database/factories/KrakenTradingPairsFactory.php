<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\KrakenTradingPairs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KrakenTradingPairs>
 */
final class KrakenTradingPairsFactory extends Factory
{
    protected $model = KrakenTradingPairs::class;

    public function definition(): array
    {
        $fiat = $this->faker->currencyCode;
        $crypto = $this->faker->currencyCode;

        return [
            'key_pair' => $crypto.$fiat,
            'crypto' => $crypto,
            'fiat' => $fiat,
            'trade_value_cents' => $this->faker->randomNumber(5),
        ];
    }
}
