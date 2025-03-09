<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use Database\Factories\UserTransactionTagFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy(UserScope::class)]
final class UserTransactionTag extends Model
{
    /** @use HasFactory<UserTransactionTagFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'tag',
        'color',
    ];

    /**
     * @return BelongsToMany<UserBudget, $this>
     */
    public function budgets(): BelongsToMany
    {
        return $this->belongsToMany(UserBudget::class, 'user_budget_tags', 'user_transaction_tag_id', 'id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
