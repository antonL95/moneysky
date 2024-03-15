<?php

declare(strict_types=1);

namespace App\Bank\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankInstitution extends Model
{
    use HasTimestamps;
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'bic',
        'transaction_total_days',
        'countries',
        'logo_url',
    ];

    protected $casts = [
        'countries' => 'array',
    ];

    /**
     * @return HasMany<UserBankAgreement>
     */
    public function userBankAgreements(): HasMany
    {
        return $this->hasMany(UserBankAgreement::class);
    }

    /**
     * @return HasMany<UserBankRequisition>
     */
    public function userBankRequisitions(): HasMany
    {
        return $this->hasMany(UserBankRequisition::class);
    }
}
