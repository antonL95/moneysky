<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Helpers\CurrencyHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class UpdateCurrencyController
{
    public function __invoke(Request $request): void
    {
        $user = $request->user();

        if (! $user instanceof User) {
            redirect()->route('login');

            return;
        }

        /** @var array{currency: string} $data */
        $data = $request->validate([
            'currency' => ['required', Rule::in(CurrencyHelper::getCurrencies())],
        ]);

        $user->currency = $data['currency'];
        $user->save();
    }
}
