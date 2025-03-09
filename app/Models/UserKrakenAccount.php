<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Models\Scopes\UserScope;
use App\Services\ConvertCurrencyService;
use Database\Factories\UserKrakenAccountFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
final class UserKrakenAccount extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserKrakenAccountFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'balance_cents',
        'api_key',
        'private_key',
    ];

    protected $casts = [
        'balance_cents' => 'int',
        'api_key' => 'encrypted',
        'private_key' => 'encrypted',
    ];

    public static function getSumOfAllAccounts(
        User $user,
        bool $withGlobalCurrency = false,
    ): int {
        $accounts = self::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->get();

        $sum = 0;
        foreach ($accounts as $wallet) {
            $sum += $wallet->balance_cents;
        }

        $currencyConvertor = new ConvertCurrencyService;

        return (int) $currencyConvertor->convert(
            new Money($sum, new Currency('USD')),
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
}
