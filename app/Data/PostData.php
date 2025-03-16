<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class PostData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $image_url,
        public readonly string $content,
        public readonly string $published_at,
    ) {}
}
