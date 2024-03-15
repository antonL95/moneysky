<?php

declare(strict_types=1);

namespace App\Bank\Models;

use App\Actions\Currency\ConvertCurrency;
use App\Bank\Enums\Status;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
class UserBankAccount extends Model
{
    use HasTimestamps;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'external_id',
        'name',
        'type',
        'balance_cents',
        'currency',
        'status',
    ];

    protected $casts = [
        'balance_cents' => 'int',
        'external_id' => 'string',
        'status' => Status::class,
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
     * @return HasMany<UserBankTransactionRaw>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(UserBankTransactionRaw::class, 'bank_id', 'id');
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
}
