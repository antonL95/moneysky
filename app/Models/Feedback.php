<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FeedbackType;
use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Feedback extends Model
{
    /** @use HasFactory<FeedbackFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'notified',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'type' => FeedbackType::class,
        ];
    }
}
