<?php

declare(strict_types=1);

namespace App\Models;

use App\HasSubscription;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasSubscription;
    use HasTimestamps;
    use MustVerifyEmail;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'demo',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @return HasMany<UserBankAccount>
     */
    public function userBankAccount(): HasMany
    {
        return $this->hasMany(UserBankAccount::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserCryptoWallets>
     */
    public function userCryptoWallet(): HasMany
    {
        return $this->hasMany(UserCryptoWallets::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserKrakenAccount>
     */
    public function userKrakenAccount(): HasMany
    {
        return $this->hasMany(UserKrakenAccount::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserStockMarket>
     */
    public function userStockMarket(): HasMany
    {
        return $this->hasMany(UserStockMarket::class, 'user_id', 'id');
    }

    /**
     * @return HasManyThrough<UserBankTransactionRaw>
     */
    public function userBankTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            UserBankTransactionRaw::class,
            UserBankAccount::class,
            'user_id',
            'bank_id',
            'id',
            'id',
        );
    }

    /**
     * @return HasMany<UserBankSession>
     */
    public function userBankSession(): HasMany
    {
        return $this->hasMany(UserBankSession::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserManualEntry>
     */
    public function userManualEntry(): HasMany
    {
        return $this->hasMany(UserManualEntry::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserSetting>
     */
    public function userSetting(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user_id', 'id');
    }

    public function canAccessPulse(): bool
    {
        return $this->email === 'loginovanton95@gmail.com';
    }
}
