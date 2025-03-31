<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\KrakenAccount\CreateKrakenAccount;
use App\Actions\KrakenAccount\UpdateKrakenAccount;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\KrakenAccount\KrakenAccountData;
use App\Data\App\KrakenAccount\UserKrakenAccountData;
use App\Enums\FlashMessageAction;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserKrakenAccount;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class UserKrakenAccountController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function index(): Response|RedirectResponse
    {
        try {
            $this->authorize('viewAny', UserKrakenAccount::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $krakenAccounts = UserKrakenAccount::get();

        $rows = [];
        foreach ($krakenAccounts as $krakenAccount) {
            $rows[] = new UserKrakenAccountData(
                $krakenAccount->id,
                Str::limit(Str::mask($krakenAccount->api_key, '*', 4), 25),
                Str::limit(Str::mask($krakenAccount->private_key, '*', 4), 25),
                $krakenAccount->balance,
            );
        }

        return Inertia::render('kraken-account/index', [
            'columns' => [
                'Id',
                'Api Key',
                'Private Key',
                'Balance',
            ],
            'rows' => $rows,
        ]);
    }

    public function store(KrakenAccountData $data, CreateKrakenAccount $createKrakenAccount): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('create', UserKrakenAccount::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $createKrakenAccount->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(KrakenAccountData $data, UserKrakenAccount $krakenAccount, UpdateKrakenAccount $updateKrakenAccount): RedirectResponse
    {
        try {
            $this->authorize('update', $krakenAccount);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }

        $updateKrakenAccount->handle($krakenAccount, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserKrakenAccount $krakenAccount): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('delete', $krakenAccount);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }

        $krakenAccount->delete();
        ProcessSnapshotJob::dispatch($user);

        return $this->success(FlashMessageAction::DELETE);
    }
}
