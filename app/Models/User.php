<?php

declare(strict_types=1);

namespace App\Models;

use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankSession;
use App\Bank\Models\UserBankTransactionRaw;
use App\Crypto\Models\UserCryptoWallets;
use App\Crypto\Models\UserKrakenAccount;
use App\ManualEntry\Models\UserManualEntry;
use App\MarketData\Models\UserStockMarket;
use App\UserSetting\Models\UserSetting;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    use HasTimestamps;
    use MustVerifyEmail;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'demo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
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

    public function canAddAdditionalResource(
        string $resource,
    ): bool {

        $unlimitedPriceId = config('services.stripe.unlimited_plan_id');

        if (!\is_string($unlimitedPriceId)) {
            return false;
        }

        if ($this->subscribed(price: $unlimitedPriceId)) {
            return true;
        }

        if ($resource === UserManualEntry::class) {
            return true;
        }

        if ($resource === UserKrakenAccount::class) {
            return true;
        }

        $plusPriceId = config('services.stripe.plus_plan_id');

        if (!\is_string($plusPriceId)) {
            return false;
        }

        $numberOfTickers = UserStockMarket::count();

        $numberOfBankAccounts = UserBankSession::count();

        $numberOfCryptoWallets = UserCryptoWallets::count();

        if ($this->subscribed(price: $plusPriceId)) {
            if ($resource === UserStockMarket::class) {
                $numberOfTickers = UserStockMarket::count();

                return $numberOfTickers < 15;
            }

            if ($resource === UserBankSession::class) {
                return $numberOfBankAccounts <= 0;
            }

            if ($resource === UserCryptoWallets::class) {
                return $numberOfCryptoWallets <= 0;
            }
        }

        if ($numberOfTickers < 3 && $numberOfCryptoWallets <= 0 && $numberOfBankAccounts <= 0 && $resource === UserStockMarket::class) {
            return true;
        }

        if ($numberOfTickers <= 0 && $numberOfCryptoWallets <= 0 && $numberOfBankAccounts <= 0 && $resource === UserCryptoWallets::class) {
            return true;
        }

        if ($numberOfTickers <= 0 && $numberOfCryptoWallets <= 0 && $numberOfBankAccounts <= 0 && $resource === UserBankSession::class) {
            return true;
        }

        return false;
    }

    public function checkSubscriptionType(
        string $type,
    ): bool {

        $plusPriceId = config('services.stripe.plus_plan_id');
        $unlimitedPriceId = config('services.stripe.unlimited_plan_id');

        if (!\is_string($plusPriceId) || !\is_string($unlimitedPriceId)) {
            return false;
        }
        if ($type === 'unlimited' && $this->subscribed(price: $unlimitedPriceId)) {
            return true;
        }

        if ($type === 'plus' && $this->subscribed(price: $plusPriceId)) {
            return true;
        }

        return false;
    }

    public function canAccessPulse(): bool
    {
        return $this->email === 'loginovanton95@gmail.com';
    }
}
