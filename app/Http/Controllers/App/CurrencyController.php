<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\Setting\CurrencyData;
use App\Enums\FlashMessageAction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class CurrencyController
{
    use HasRedirectWithFlashMessage;

    public function __invoke(CurrencyData $data): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            redirect()->route('login');
        }

        $user->currency = $data->currency;
        $user->save();

        return $this->success(FlashMessageAction::UPDATE);
    }
}
