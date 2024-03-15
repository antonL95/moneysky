<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
class UserSetting extends Model
{
    private const string DEFAULT_CURRENCY = 'USD';

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
        $userSetting = self::whereKey('currency')->first();

        return $userSetting?->value === null || $userSetting->value === ''
            ? self::DEFAULT_CURRENCY
            : $userSetting->value;
    }
}
