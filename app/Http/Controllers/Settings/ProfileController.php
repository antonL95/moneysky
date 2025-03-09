<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Concerns\HasRedirectWithFlashMessage;
use App\Enums\FlashMessageAction;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\UserBankAccount;
use App\Models\UserCryptoWallet;
use App\Models\UserKrakenAccount;
use App\Models\UserManualEntry;
use App\Models\UserStockMarket;
use App\Services\BankService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

final class ProfileController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request, BankService $bankService): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('forceDelete', $user);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }

        if ($user->subscribed()) {
            return back()->with(
                'flash',
                [
                    'title' => 'Cancel subscription before deleting',
                    'description' => 'Before deleting account please cancel subscription',
                    'type' => 'danger',
                ],
            );
        }

        $user->userCryptoWallet()->each(fn (UserCryptoWallet $userCryptoWallets) => $userCryptoWallets->forceDelete());
        $user->userKrakenAccount()->each(fn (UserKrakenAccount $userKrakenAccount) => $userKrakenAccount->forceDelete());
        $user->userManualEntry()->each(fn (UserManualEntry $userManualEntry) => $userManualEntry->forceDelete());
        $user->userStockMarket()->each(fn (UserStockMarket $userStockMarket) => $userStockMarket->forceDelete());
        $user->userBankAccount()->each(fn (UserBankAccount $userBankAccount) => $userBankAccount->forceDelete());

        $bankService->deleteUserRequisitions($user);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('home');
    }
}
