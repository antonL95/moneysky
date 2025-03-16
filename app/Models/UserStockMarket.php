<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use App\Services\ConvertCurrencyService;
use Database\Factories\UserStockMarketFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
final class UserStockMarket extends Model
{
    /** @use HasFactory<UserStockMarketFactory> */
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'user_id',
        'ticker',
        'amount',
        'balance_cents',
    ];

    public static function getSumOfAllTickers(
        User $user,
        bool $withGlobalCurrency = false,
    ): int {
        $tickers = self::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->get();

        $sum = 0;
        foreach ($tickers as $ticker) {
            $sum += $ticker->amount * $ticker->balance_cents;
        }

        $currencyConvertor = new ConvertCurrencyService;

        return (int) $currencyConvertor->convert(
            new Money((int) $sum, new Currency('USD')),
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
     * @return Attribute<string, never>
     */
    protected function balance(): Attribute
    {
        $user = Auth::user();

        if ($user === null) {
            return Attribute::make(
                get: static fn (mixed $value, mixed $attributes): string => '0',
            );
        }

        if ($this->balance_cents === null || $this->amount === null) {
            return Attribute::make(
                get: static fn (mixed $value, mixed $attributes): string => '0',
            );
        }

        $currencyConvertor = new ConvertCurrencyService;

        $finalAmount = (int) round($this->amount * $this->balance_cents);
        $balance = (int) $currencyConvertor->convert(
            new Money($finalAmount, new Currency('USD')),
            new Currency($user->currency), // @phpstan-ignore-line
        )->getAmount();

        return Attribute::make(
            get: static fn (mixed $value, mixed $attributes): string => (string) Number::currency($balance / 100, $user->currency),
        );
    }

    /**
     * @return array{
     *     amount: 'float',
     *     balance_cents: 'int',
     * }
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'balance_cents' => 'int',
        ];
    }
}
