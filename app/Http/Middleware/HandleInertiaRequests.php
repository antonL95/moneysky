<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\App\FlashData;
use App\Data\App\UserData;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Override;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    #[Override]
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function share(Request $request): array
    {
        /* @phpstan-ignore-next-line */
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'flash' => FlashData::optional($request->session()->pull('flash')),
            'auth' => [
                'user' => $request->user() === null
                    ? null
                    : new UserData(
                        $request->user()->id,
                        $request->user()->name ?? '',
                        $request->user()->email,
                        $request->user()->currency,
                        $request->user()->subscribed(),
                        $request->user()->email_verified,
                    ),
            ],
        ];
    }
}
