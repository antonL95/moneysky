<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->subscribed()) {
            return redirect('/billing');
        }

        return $next($request);
    }
}
