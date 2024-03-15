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
class UserBankRequisition extends Model
{
    use HasTimestamps;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_bank_agreement_id',
        'bank_institution_id',
        'external_id',
        'status',
        'accounts',
        'user_language',
        'link',
    ];

    protected $casts = [
        'status' => 'array',
        'accounts' => 'array',
        'external_id' => 'string',
    ];

    /**
     * @return BelongsTo<BankInstitution, self>
     */
    public function bankInstitution(): BelongsTo
    {
        return $this->belongsTo(BankInstitution::class, 'bank_institution_id');
    }

    /**
     * @return BelongsTo<UserBankAgreement, self>
     */
    public function userBankAgreement(): BelongsTo
    {
        return $this->belongsTo(UserBankAgreement::class, 'user_bank_agreement_id');
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
