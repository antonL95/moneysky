<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BankInstitutionFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class BankInstitution extends Model
{
    /** @use HasFactory<BankInstitutionFactory> */
    use HasFactory;

    use HasTimestamps;
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'bic',
        'transaction_total_days',
        'countries',
        'logo_url',
        'active',
    ];

    protected $casts = [
        'countries' => 'array',
        'active' => 'boolean',
    ];

    /**
     * @return HasMany<UserBankSession, $this>
     */
    public function userBankSessions(): HasMany
    {
        return $this->hasMany(UserBankSession::class);
    }
}
