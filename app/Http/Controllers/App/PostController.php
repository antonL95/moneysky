<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Data\PostData;
use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class PostController
{
    public function show(Post $post): Response
    {
        return Inertia::render('Post/Index', [
            'post' => PostData::from([
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => Str::markdown($post->content),
                'image_url' => $post->image_url === null ? null : Storage::url($post->image_url),
                'published_at' => $post->published_at->format('d/m/Y'), // @phpstan-ignore-line
            ]),
        ]);
    }

    /**
     * @return Collection<int, array{id: int, title: string, slug: string|null, image_url: string|null, published_at: mixed}>
     */
    public function index(): Collection
    {
        return Post::whereNotNull('published_at') // @phpstan-ignore-line
            ->latest()
            ->limit(3)
            ->get()
            ->map(
                fn (Post $post): PostData => PostData::from([
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'image_url' => $post->image_url === null
                        ? null
                        : Storage::url($post->image_url),
                    'content' => '',
                    'published_at' => $post->published_at?->format('d/m/Y'),
                ]),
            );
    }
}
