<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use Database\Factories\UserBankSessionFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy(UserScope::class)]
final class UserBankSession extends Model
{
    /** @use HasFactory<UserBankSessionFactory> */
    use HasFactory;

    use HasTimestamps;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_institution_id',
        'link',
        'requisition_id',
        'agreement_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<BankInstitution, $this>
     */
    public function bankInstitution(): BelongsTo
    {
        return $this->belongsTo(BankInstitution::class);
    }

    /**
     * @return HasMany<UserBankAccount, $this>
     */
    public function userBankAccounts(): HasMany
    {
        return $this->hasMany(UserBankAccount::class, 'user_bank_session_id', 'id');
    }
}
