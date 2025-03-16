<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Enums\ChainType;
use App\Models\Scopes\UserScope;
use App\Services\ConvertCurrencyService;
use Database\Factories\UserCryptoWalletFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
final class UserCryptoWallet extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserCryptoWalletFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'wallet_address',
        'chain_type',
        'balance_cents',
        'tokens',
    ];

    protected $casts = [
        'tokens' => 'array',
        'balance_cents' => 'int',
        'chain_type' => ChainType::class,
        'wallet_address' => 'string',
    ];

    public static function getSumOfUserWallets(
        User $user,
        bool $withGlobalCurrency = false,
    ): int {
        $wallets = self::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->get();

        $sum = 0;
        foreach ($wallets as $wallet) {
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
