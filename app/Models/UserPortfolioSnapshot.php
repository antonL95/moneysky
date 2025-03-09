<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Models\Scopes\UserScope;
use Database\Factories\UserPortfolioSnapshotFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(UserScope::class)]
final class UserPortfolioSnapshot extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserPortfolioSnapshotFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance_cents',
        'change',
        'aggregate_date',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<UserPortfolioAsset, $this>
     */
    public function assetSnapshots(): HasMany
    {
        return $this->hasMany(UserPortfolioAsset::class, 'snapshot_id', 'id');
    }

    /**
     * @return array{
     *     balance_cents: 'int',
     *     change: 'float',
     *     aggregate_date: 'date',
     * }
     */
    public function casts(): array
    {
        return [
            'balance_cents' => 'int',
            'change' => 'float',
            'aggregate_date' => 'date',
        ];
    }
}
