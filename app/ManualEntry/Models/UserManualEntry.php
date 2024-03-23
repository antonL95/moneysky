<?php

declare(strict_types=1);

namespace App\ManualEntry\Models;

use App\Actions\Currency\ConvertCurrency;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\UserSetting\Models\UserSetting;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Money\Currency;
use Money\Money;

#[ScopedBy(UserScope::class)]
class UserManualEntry extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'amount_cents',
        'currency',
    ];

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getSumWithCurrency(
        User $user,
    ): int {
        $userManualEntries = self::where('user_id', $user->id)->get();
        $currencyConvertor = new ConvertCurrency;
        $sumUsd = 0;

        /** @var UserManualEntry $manualEntry */
        foreach ($userManualEntries as $manualEntry) {
            $balance = (int) $currencyConvertor->convert(
                new Money((int) $manualEntry->amount_cents, new Currency($manualEntry->currency === '' ? 'USD' : $manualEntry->currency)),
                new Currency('USD'),
            )->getAmount();

            $sumUsd += $balance;
        }

        return (int) $currencyConvertor->convert(
            new Money((int) $sumUsd, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'amount_cents' => 'int',
        ];
    }
}
