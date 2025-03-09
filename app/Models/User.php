<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasSubscription;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

final class User extends Authenticatable implements FilamentUser, MustVerifyEmailContract
{
    use Billable;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasSubscription;
    use HasTimestamps;
    use MustVerifyEmail;
    use Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'demo',
        'currency',
        'email_verified_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    /**
     * @var list<string>
     */
    protected $guarded = [
        'is_admin',
    ];

    /**
     * @return HasMany<UserBankAccount, $this>
     */
    public function userBankAccount(): HasMany
    {
        return $this->hasMany(UserBankAccount::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserCryptoWallet, $this>
     */
    public function userCryptoWallet(): HasMany
    {
        return $this->hasMany(UserCryptoWallet::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserKrakenAccount, $this>
     */
    public function userKrakenAccount(): HasMany
    {
        return $this->hasMany(UserKrakenAccount::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserStockMarket, $this>
     */
    public function userStockMarket(): HasMany
    {
        return $this->hasMany(UserStockMarket::class, 'user_id', 'id');
    }

    /**
     * @return HasManyThrough<UserBankTransactionRaw, UserBankAccount, $this>
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
     * @return HasMany<UserBankSession, $this>
     */
    public function userBankSession(): HasMany
    {
        return $this->hasMany(UserBankSession::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserTransaction, $this>
     */
    public function userTransaction(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserManualEntry, $this>
     */
    public function userManualEntry(): HasMany
    {
        return $this->hasMany(UserManualEntry::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserPortfolioSnapshot, $this>
     */
    public function dailySnapshots(): HasMany
    {
        return $this->hasMany(UserPortfolioSnapshot::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserPortfolioAsset, $this>
     */
    public function assetSnapshots(): HasMany
    {
        return $this->hasMany(UserPortfolioAsset::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserTransactionAggregate, $this>
     */
    public function transactionsAggregate(): HasMany
    {
        return $this->hasMany(UserTransactionAggregate::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserBudget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(UserBudget::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserSocialProvider, $this>
     */
    public function socialProviders(): HasMany
    {
        return $this->hasMany(UserSocialProvider::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<Feedback, $this>
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id', 'id');
    }

    public function canAccessPulse(): bool
    {
        return $this->is_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function isSubscribed(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, mixed $attributes): bool => $this->subscribed(),
        );
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function emailVerified(): Attribute
    {
        return Attribute::make(
            get: static fn (mixed $value, array $attributes): bool => $attributes['email_verified_at'] !== null,
        );
    }

    /**
     * @return Attribute<non-falsy-string, never>
     */
    protected function avatar(): Attribute
    {
        $name = mb_trim(
            collect(explode(' ', $this->name ?? ''))->map(fn (string $segment): string => mb_substr($segment, 0, 1))->join(' '),
        );

        return Attribute::make(
            get: static fn (mixed $value, mixed $attributes): string => 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF',
        );
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
