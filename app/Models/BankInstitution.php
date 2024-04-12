<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankInstitution extends Model
{
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
    ];

    protected $casts = [
        'countries' => 'array',
    ];

    /**
     * @return HasMany<UserBankSession>
     */
    public function userBankSessions(): HasMany
    {
        return $this->hasMany(UserBankSession::class);
    }
}
