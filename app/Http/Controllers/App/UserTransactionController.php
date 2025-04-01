<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Dashboard\CreateTransaction;
use App\Actions\Dashboard\UpdateTransaction;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\Dashboard\TransactionData;
use App\Enums\FlashMessageAction;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class UserTransactionController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function store(TransactionData $data, CreateTransaction $createTransaction): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('create', UserTransaction::class);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return redirect()->route('login');
        }
        // @codeCoverageIgnoreEnd

        $createTransaction->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(TransactionData $data, UserTransaction $userTransaction, UpdateTransaction $updateTransaction): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('update', $userTransaction);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return redirect()->route('login');
        }
        // @codeCoverageIgnoreEnd

        $updateTransaction->handle($user, $userTransaction, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }
}
