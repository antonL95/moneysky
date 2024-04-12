<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(UserScope::class)]
class UserTransactionTag extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'tag',
        'color',
    ];

    /**
     * @return BelongsTo<User, UserTransactionTag>
     */
    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
