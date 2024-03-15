<?php

declare(strict_types=1);

namespace App\MarketData\Models;

use App\Models\Scopes\UserScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
class UserStockMarket extends Model
{
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'ticker',
        'amount',
        'price_cents',
    ];

    protected $casts = [
        'amount' => 'float',
        'price_cents' => 'int',
    ];

    /**
     * @return BelongsTo<User, UserStockMarket>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
