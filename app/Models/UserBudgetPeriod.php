<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use Database\Factories\UserBudgetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserBudgetPeriod extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserBudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'user_budget_id',
        'start_date',
        'end_date',
        'balance_cents',
    ];

    /**
     * @return BelongsTo<UserBudget, $this>
     */
    public function userBudget(): BelongsTo
    {
        return $this->belongsTo(UserBudget::class);
    }

    /**
     * @return array{
     *     start_date: 'date',
     *     end_date: 'date',
     * }
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
}
