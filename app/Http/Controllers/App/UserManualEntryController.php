<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\ManualEntry\CreateManualEntry;
use App\Actions\ManualEntry\UpdateManualEntry;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\ManualEntry\ManualEntryData;
use App\Data\App\ManualEntry\UserManualEntryData;
use App\Enums\FlashMessageAction;
use App\Helpers\CurrencyHelper;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserManualEntry;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class UserManualEntryController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $userManualEntries = $user->userManualEntry()->get();

        $rows = $userManualEntries->map(
            fn (UserManualEntry $userManualEntry): UserManualEntryData => new UserManualEntryData(
                $userManualEntry->id,
                $userManualEntry->name,
                Str::limit($userManualEntry->description ?? '', 30),
                $userManualEntry->balance,
                $userManualEntry->balance_cents / 100,
                $userManualEntry->currency,
            ),
        );

        return Inertia::render('manual-entry/index', [
            'columns' => [
                'Id',
                'Name',
                'Description',
                'Balance',
            ],
            'rows' => $rows,
            'currencies' => CurrencyHelper::getCurrencies(),
        ]);
    }

    public function store(ManualEntryData $data, CreateManualEntry $createManualEntry): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('create', UserManualEntry::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $createManualEntry->handle($user, ManualEntryData::from($data));

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(ManualEntryData $data, UserManualEntry $manualEntry, UpdateManualEntry $updateManualEntry): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('update', $manualEntry);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }
        // @codeCoverageIgnoreEnd

        $updateManualEntry->handle($user, $manualEntry, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserManualEntry $manualEntry): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('delete', $manualEntry);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }
        // @codeCoverageIgnoreEnd

        $manualEntry->delete();
        ProcessSnapshotJob::dispatch($user);

        return $this->success(FlashMessageAction::DELETE);
    }
}
