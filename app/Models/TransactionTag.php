<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TransactionTagFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TransactionTag extends Model
{
    /** @use HasFactory<TransactionTagFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'tag',
        'color',
    ];

    /**
     * @return HasMany<UserTransactionAggregate, $this>
     */
    public function transactionAggregates(): HasMany
    {
        return $this->hasMany(UserTransactionAggregate::class, 'transaction_tag_id', 'id');
    }

    /**
     * @return HasMany<UserTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'transaction_tag_id', 'id');
    }

    /**
     * @return BelongsToMany<UserBudget, $this>
     */
    public function budgets(): BelongsToMany
    {
        return $this->belongsToMany(UserBudget::class, 'user_budget_tags', 'transaction_tag_id', 'id'); // @phpstan-ignore-line
    }
}
