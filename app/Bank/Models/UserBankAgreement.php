<?php

declare(strict_types=1);

namespace App\Bank\Models;

use App\Models\Scopes\UserScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy(UserScope::class)]
class UserBankAgreement extends Model
{
    use HasTimestamps;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_institution_id',
        'external_id',
        'max_historical_days',
        'access_valid_for_days',
        'access_scope',
        'accepted_at',
    ];

    protected $casts = [
        'access_scope' => 'array',
        'accepted_at' => 'datetime',
        'external_id' => 'string',
    ];

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<BankInstitution, self>
     */
    public function bankInstitution(): BelongsTo
    {
        return $this->belongsTo(BankInstitution::class, 'bank_institution_id');
    }
}
