<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Enums\BankAccountStatus;
use App\Models\Scopes\UserScope;
use App\Services\ConvertCurrencyService;
use Database\Factories\UserBankAccountFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
final class UserBankAccount extends Model
{
    /** @use HasFactory<UserBankAccountFactory> */
    use HasBalanceAttribute,HasFactory, HasTimestamps;

    protected $fillable = [
        'user_id',
        'user_bank_session_id',
        'external_id',
        'resource_id',
        'name',
        'iban',
        'balance_cents',
        'currency',
        'access_expires_at',
        'status',
    ];

    public static function getSumOfAllUserBankAccounts(
        User $user,
        bool $withGlobalCurrency = false,
    ): int {
        $userBankAccounts = self::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->get();

        $currencyConvertor = new ConvertCurrencyService;
        $sumUsd = 0;

        /** @var UserBankAccount $bankAccount */
        foreach ($userBankAccounts as $bankAccount) {
            $balance = (int) $currencyConvertor->convert(
                new Money((int) $bankAccount->balance_cents, new Currency($bankAccount->currency === '' ? 'USD' : $bankAccount->currency)),
                new Currency('USD'),
            )->getAmount();

            $sumUsd += $balance;
        }

        return (int) $currencyConvertor->convert(
            new Money($sumUsd, new Currency('USD')),
            new Currency($withGlobalCurrency ? 'USD' : $user->currency), // @phpstan-ignore-line
        )->getAmount();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<UserBankSession, $this>
     */
    public function userBankSession(): BelongsTo
    {
        return $this->belongsTo(UserBankSession::class, 'user_bank_session_id');
    }

    /**
     * @return HasMany<UserBankTransactionRaw, $this>
     */
    public function userBankTransactionRaw(): HasMany
    {
        return $this->hasMany(UserBankTransactionRaw::class, 'user_bank_account_id', 'id');
    }

    /**
     * @return HasMany<UserTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'user_bank_account_id', 'id');
    }

    /**
     * @return HasOneThrough<BankInstitution, UserBankSession, $this>
     */
    public function institution(): HasOneThrough
    {
        return $this->hasOneThrough(
            BankInstitution::class,
            UserBankSession::class,
            'id',
            'id',
            'user_bank_session_id',
            'bank_institution_id',
        );
    }

    /**
     * @return array{
     *     'balance_cents': 'int',
     *     'resource_id': 'string',
     *     'external_id': 'string',
     *     'access_expires_at': 'datetime',
     *     'iban': 'encrypted',
     *     'status': 'App\Enums\BankAccountStatus',
     * }
     */
    protected function casts(): array
    {
        return [
            'balance_cents' => 'int',
            'resource_id' => 'string',
            'external_id' => 'string',
            'access_expires_at' => 'datetime',
            'iban' => 'encrypted',
            'status' => BankAccountStatus::class,
        ];
    }
}
