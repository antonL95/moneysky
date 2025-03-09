<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasBalanceAttribute;
use App\Enums\AssetType;
use App\Models\Scopes\UserScope;
use Database\Factories\UserPortfolioAssetFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
final class UserPortfolioAsset extends Model
{
    use HasBalanceAttribute;

    /** @use HasFactory<UserPortfolioAssetFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'snapshot_id',
        'asset_type',
        'balance_cents',
        'change',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<UserPortfolioSnapshot, $this>
     */
    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(UserPortfolioSnapshot::class, 'snapshot_id');
    }

    /**
     * @return array{
     *     balance_cents: 'int',
     *     change: 'float',
     *     asset_type: 'App\Enums\AssetType',
     * }
     */
    protected function casts(): array
    {
        return [
            'balance_cents' => 'int',
            'change' => 'float',
            'asset_type' => AssetType::class,
        ];
    }
}
