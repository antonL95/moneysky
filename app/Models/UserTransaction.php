<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ScopedBy(UserScope::class)]
class UserTransaction extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'user_bank_account_id',
        'balance_cents',
        'currency',
        'description',
        'booked_at',
        'transaction_tag_id',
        'user_transaction_tag_id',
        'user_bank_transaction_raw_id',
    ];

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo<UserBankAccount, self>
     */
    public function userBankAccount(): BelongsTo
    {
        return $this->belongsTo(UserBankAccount::class, 'user_bank_account_id', 'id');
    }

    /**
     * @return BelongsTo<UserBankTransactionRaw, self>
     */
    public function userBankTransactionRaw(): BelongsTo
    {
        return $this->belongsTo(UserBankTransactionRaw::class, 'user_bank_transaction_raw_id', 'id');
    }

    /**
     * @return HasOne<TransactionTag>
     */
    public function transactionTag(): HasOne
    {
        return $this->hasOne(TransactionTag::class, 'id', 'transaction_tag_id');
    }

    /**
     * @return HasOne<UserTransactionTag>
     */
    public function userTransactionTag(): HasOne
    {
        return $this->hasOne(UserTransactionTag::class, 'id', 'user_transaction_tag_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booked_at' => 'datetime',
        ];
    }
}
