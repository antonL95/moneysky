<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use Database\Factories\UserBankTransactionRawFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserBankTransactionRaw extends Model
{
    /** @use HasFactory<UserBankTransactionRawFactory> */
    use HasBalanceAttribute, HasFactory, HasTimestamps;

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
        'processed',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'balance_cents' => 'integer',
        'currency_exchange' => 'array',
        'processed' => 'boolean',
    ];

    protected $table = 'user_bank_transactions_raw';

    /**
     * @return BelongsTo<UserBankAccount, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(UserBankAccount::class, 'user_bank_account_id', 'id');
    }
}
