<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Dashboard\ShowTransaction;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Enums\FlashMessageAction;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final readonly class ShowTransactionController
{
    use HasRedirectWithFlashMessage;

    public function __invoke(UserTransaction $transaction, ShowTransaction $action): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $action->handle($user, $transaction);

        return $this->success(FlashMessageAction::UPDATE);
    }
}
