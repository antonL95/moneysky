<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Models\Scopes\UserScope;
use App\Services\ConvertCurrencyService;
use Database\Factories\UserManualEntryFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
final class UserManualEntry extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserManualEntryFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'balance_cents',
        'currency',
    ];

    public static function getSumWithCurrency(
        User $user,
        bool $withGlobalCurrency = false,
    ): int {
        $userManualEntries = self::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->get();
        $currencyConvertor = new ConvertCurrencyService;
        $sumUsd = 0;

        /** @var UserManualEntry $manualEntry */
        foreach ($userManualEntries as $manualEntry) {
            $balance = (int) $currencyConvertor->convert(
                new Money(
                    (int) $manualEntry->balance_cents, new Currency(
                        $manualEntry->currency === '' ? 'USD' : $manualEntry->currency,
                    ),
                ),
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
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<UserTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'balance_cents' => 'int',
        ];
    }
}
