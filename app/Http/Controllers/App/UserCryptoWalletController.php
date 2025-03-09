<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CryptoWallet\CreateCryptoWallet;
use App\Actions\CryptoWallet\UpdateCryptoWallet;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\CryptoWallet\CryptoWalletData;
use App\Data\App\CryptoWallet\UserCryptoWalletData;
use App\Enums\FlashMessageAction;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserCryptoWallet;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class UserCryptoWalletController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function index(): Response|RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('viewAny', UserCryptoWallet::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $cryptoWallets = UserCryptoWallet::get();

        $rows = [];
        foreach ($cryptoWallets as $cryptoWallet) {
            $rows[] = new UserCryptoWalletData(
                $cryptoWallet->id,
                $cryptoWallet->wallet_address,
                $cryptoWallet->chain_type,
                $cryptoWallet->chain_type->getPrettyName(),
                $cryptoWallet->balance,
            );
        }

        return Inertia::render('CryptoWallet/Index', [
            'columns' => [
                'Id',
                'Address',
                'Chain',
                'Balance',
            ],
            'rows' => $rows,
        ]);
    }

    public function store(CryptoWalletData $data, CreateCryptoWallet $createCryptoWallet): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('create', UserCryptoWallet::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $createCryptoWallet->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(CryptoWalletData $data, UserCryptoWallet $digitalWallet, UpdateCryptoWallet $updateCryptoWallet): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('update', $digitalWallet);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }

        $updateCryptoWallet->handle($digitalWallet, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserCryptoWallet $digitalWallet): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('delete', $digitalWallet);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }

        $digitalWallet->delete();
        ProcessSnapshotJob::dispatch($user);

        return $this->success(FlashMessageAction::DELETE);
    }
}
