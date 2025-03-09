<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use const JSON_THROW_ON_ERROR;

use App\Models\User;
use App\Models\UserSocialProvider;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class SocialController
{
    public function redirect(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        return Socialite::driver($driver)->redirect();
    }

    public function callback(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        try {
            $socialiteUser = Socialite::driver($driver)->user();
            $providerUser = $this->findOrCreateProviderUser($socialiteUser, $driver);

            if ($providerUser instanceof RedirectResponse) {
                return $providerUser;
            }

            if (! $providerUser->user instanceof Authenticatable) {
                throw new RuntimeException;
            }

            Auth::login($providerUser->user);

            return redirect()->route('dashboard.index');
        } catch (Exception) {
            return redirect()->route('login')->with('error', 'An error occurred during authentication. Please try again.');
        }
    }

    private function dynamicallySetSocialProviderCredentials(string $provider): void
    {
        Config::set('services.'.$provider.'.redirect', '/auth/'.$provider.'/callback');
    }

    private function findOrCreateProviderUser(SocialUser $socialiteUser, string $driver): UserSocialProvider|RedirectResponse
    {
        $providerUser = UserSocialProvider::where('provider_slug', $driver)
            ->where('provider_user_id', $socialiteUser->getId())
            ->first();

        if ($providerUser !== null) {
            return $providerUser;
        }

        $user = User::where('email', $socialiteUser->getEmail())->first();

        if ($user !== null) {
            $existingProvider = $user->socialProviders()->first();
            if ($existingProvider) {
                return redirect()->route('login')->with(
                    'error',
                    'This email is already associated with another provider. Please login using that provider.',
                );
            }
        }

        return DB::transaction(function () use ($socialiteUser, $driver, $user): UserSocialProvider {
            $user ??= $this->createUser($socialiteUser);

            return $this->createSocialProviderUser($user, $socialiteUser, $driver);
        });
    }

    private function createUser(SocialUser $socialiteUser): User
    {
        $user = User::create([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'email_verified_at' => now(),
        ]);

        Event::dispatch(new Verified($user));

        return $user;
    }

    private function createSocialProviderUser(User $user, SocialUser $socialiteUser, string $driver): UserSocialProvider
    {
        return $user->socialProviders()->create([
            'provider_slug' => $driver,
            'provider_user_id' => $socialiteUser->getId(),
            'nickname' => $socialiteUser->getNickname(),
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'avatar' => $socialiteUser->getAvatar(),
            'provider_data' => $socialiteUser instanceof SocialiteUser
                ? json_encode($socialiteUser->user, JSON_THROW_ON_ERROR)
                : null,
            'token' => $socialiteUser instanceof SocialiteUser
                ? $socialiteUser->token
                : '',
            'refresh_token' => $socialiteUser instanceof SocialiteUser
                ? $socialiteUser->refreshToken
                : null,
            'token_expires_at' => $socialiteUser instanceof SocialiteUser && $socialiteUser->expiresIn !== null
                ? now()->addSeconds($socialiteUser->expiresIn)
                : null,
        ]);
    }
}
