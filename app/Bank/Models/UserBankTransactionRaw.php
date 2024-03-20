<?php

declare(strict_types=1);

namespace App\Bank\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBankTransactionRaw extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'external_id',
        'user_bank_account_id',
        'balance_cents',
        'currency',
        'currency_exchange',
        'additional_information',
        'remittance_information',
        'booked_at',
        'merchant_category_code',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'balance_cents' => 'integer',
        'currency_exchange' => 'array',
    ];

    protected $table = 'user_bank_transactions_raw';

    /**
     * @return BelongsTo<UserBankAccount, UserBankTransactionRaw>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(UserBankAccount::class, 'user_bank_account_id', 'id');
    }
}
