<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_url',
        'published_at',
    ];

    /**
     * @return array{
     *     published_at: 'datetime'
     * }
     */
    public function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
