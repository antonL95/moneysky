<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Models\Scopes\UserScope;
use Database\Factories\UserTransactionAggregateFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
final class UserTransactionAggregate extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserTransactionAggregateFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_tag_id',
        'aggregate_date',
        'balance_cents',
        'change',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<TransactionTag, $this>
     */
    public function transactionTag(): BelongsTo
    {
        return $this->belongsTo(TransactionTag::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aggregate_date' => 'date',
            'change' => 'float',
        ];
    }
}
