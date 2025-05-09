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
use App\Models\User;
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
        try {
            $this->authorize('viewAny', UserCryptoWallet::class);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

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

        return Inertia::render('crypto-wallet/index', [
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
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('create', UserCryptoWallet::class);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

        $createCryptoWallet->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(CryptoWalletData $data, UserCryptoWallet $digitalWallet, UpdateCryptoWallet $updateCryptoWallet): RedirectResponse
    {
        try {
            $this->authorize('update', $digitalWallet);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

        $updateCryptoWallet->handle($digitalWallet, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserCryptoWallet $digitalWallet): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('delete', $digitalWallet);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

        $digitalWallet->delete();
        ProcessSnapshotJob::dispatch($user);

        return $this->success(FlashMessageAction::DELETE);
    }
}
