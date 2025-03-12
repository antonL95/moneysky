<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Dashboard\CreateTransaction;
use App\Actions\Dashboard\UpdateTransaction;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\Dashboard\TransactionData;
use App\Enums\FlashMessageAction;
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
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('create', UserTransaction::class);
        } catch (AuthorizationException) {
            return redirect()->route('login');
        }

        $createTransaction->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(TransactionData $data, UserTransaction $userTransaction, UpdateTransaction $updateTransaction): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('update', $userTransaction);
        } catch (AuthorizationException) {
            return redirect()->route('login');
        }

        $updateTransaction->handle($user, $userTransaction, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }
}
