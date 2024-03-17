<?php

declare(strict_types=1);

namespace App\Crypto\Models;

use App\Models\Scopes\UserScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
class UserKrakenAccount extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'balance_cents',
        'api_key',
        'private_key',
    ];

    protected $casts = [
        'balance_cents' => 'int',
        'api_key' => 'encrypted',
        'private_key' => 'encrypted',
    ];

    /**
     * @return BelongsTo<User, UserKrakenAccount>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
