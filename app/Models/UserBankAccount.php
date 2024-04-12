<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\Currency\ConvertCurrency;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
class UserBankAccount extends Model
{
    use HasFactory;
    use HasTimestamps;
    use SoftDeletes;

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
    ];

    protected $table = 'user_bank_accounts';

    /**
     * @return BelongsTo<User, UserBankAccount>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<UserBankSession, UserBankAccount>
     */
    public function userBankSession(): BelongsTo
    {
        return $this->belongsTo(UserBankSession::class, 'user_bank_session_id');
    }

    /**
     * @return HasMany<UserBankTransactionRaw>
     */
    public function userBankTransactionRaw(): HasMany
    {
        return $this->hasMany(UserBankTransactionRaw::class, 'user_bank_account_id', 'id');
    }

    /**
     * @return HasMany<UserTransaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'user_bank_account_id', 'id');
    }

    /**
     * @return HasOneThrough<BankInstitution>
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

    public static function getSumOfAllUserBankAccounts(
        User $user,
    ): int {
        $userBankAccounts = self::where('user_id', $user->id)->get();
        $currencyConvertor = new ConvertCurrency;
        $sumUsd = 0;
        foreach ($userBankAccounts as $bankAccount) {
            $balance = (int) $currencyConvertor->convert(
                new Money((int) $bankAccount->balance_cents, new Currency($bankAccount->currency === '' ? 'USD' : $bankAccount->currency)),
                new Currency('USD'),
            )->getAmount();

            $sumUsd += $balance;
        }

        return (int) $currencyConvertor->convert(
            new Money($sumUsd, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance_cents' => 'int',
            'resource_id' => 'string',
            'external_id' => 'string',
            'access_expires_at' => 'timestamp',
        ];
    }
}
