<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\CustomAppException;
use App\Models\UserBankSession;
use App\Services\BankService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserBankAccountRedirect extends Controller
{
    public function __construct(
        protected readonly BankService $connectBankAccounts,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $ref = $request->get('ref');

        if (!\is_string($ref)) {
            return redirect()->route('app.bank-accounts');
        }

        try {
            $this->connectBankAccounts->create($user, $ref);

            return redirect()->route('app.bank-accounts');
        } catch (CustomAppException) {
            return redirect()->route('app.bank-accounts');
        }
    }
}
