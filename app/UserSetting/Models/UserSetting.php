<?php

declare(strict_types=1);

namespace App\UserSetting\Models;

use App\Enums\CacheKeys;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\UserSetting\Enums\UserSettingKeys;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

#[ScopedBy(UserScope::class)]
class UserSetting extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * @return BelongsTo<User, UserSetting>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return non-empty-string
     */
    public static function getCurrencyWithDefault(): string
    {
        if (Cache::missing(CacheKeys::USER_CURRENCY->value.'-'.auth()->id())) {
            $userSetting = self::where('key', '=', UserSettingKeys::CURRENCY->value)->first();
            if ($userSetting !== null) {
                Cache::put(CacheKeys::USER_CURRENCY->value.'-'.auth()->id(), $userSetting->value);
            }
            $userCurrency = $userSetting?->value;
        } else {
            $userCurrency = Cache::get(CacheKeys::USER_CURRENCY->value.'-'.auth()->id());
        }

        $defaultCurrency = config('app.default_currency');

        if (!\is_string($defaultCurrency) || $defaultCurrency === '') {
            throw new \RuntimeException('Default currency is not set in the config file');
        }

        return $userCurrency === '' || !\is_string($userCurrency)
            ? $defaultCurrency
            : $userCurrency;
    }
}
