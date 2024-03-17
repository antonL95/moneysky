<?php

declare(strict_types=1);

namespace Database\Factories\Bank\Models;

use App\Bank\Models\UserBankTransactionRaw;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBankTransactionRaw>
 */
class UserBankTransactionRawFactory extends Factory
{
    protected $model = UserBankTransactionRaw::class;

    public function definition(): array
    {
        return [

        ];
    }
}
