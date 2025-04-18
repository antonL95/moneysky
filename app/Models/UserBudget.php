<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Models\Scopes\UserScope;
use Database\Factories\UserBudgetFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(UserScope::class)]
final class UserBudget extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserBudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'balance_cents',
        'currency',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<TransactionTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TransactionTag::class, 'user_budget_tags', 'user_budget_id', 'transaction_tag_id');
    }

    /**
     * @return HasMany<UserBudgetPeriod, $this>
     */
    public function periods(): HasMany
    {
        return $this->hasMany(UserBudgetPeriod::class);
    }
}
