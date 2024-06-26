<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChainType;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
class UserCryptoWallets extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'wallet_address',
        'chain_type',
        'balance_cents',
        'tokens',
    ];

    protected $casts = [
        'tokens' => 'array',
        'balance_cents' => 'int',
        'chain_type' => ChainType::class,
        'wallet_address' => 'string',
    ];

    /**
     * @return BelongsTo<User, UserCryptoWallets>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
