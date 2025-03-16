<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final readonly class Subscribed
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse|null|JsonResponse
    {
        $user = $request->user();
        // @codeCoverageIgnoreStart
        if ($user === null) {
            return redirect(route('login'));
        }
        // @codeCoverageIgnoreEnd

        if (! $user->subscribed()) {
            return redirect(route('subscribe'));
        }

        return $next($request); // @phpstan-ignore-line
    }
}
