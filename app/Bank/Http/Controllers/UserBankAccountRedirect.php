<?php

declare(strict_types=1);

namespace App\Bank\Http\Controllers;

use App\Bank\Services\BankAccounts;
use App\Exceptions\CustomAppException;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserBankAccountRedirect extends Controller
{

    public function __construct(
        protected readonly BankAccounts $connectBankAccounts,
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
            session()->put('bank-account-error', 'Something went wrong with connecting bank account!');

            return redirect()->route('app.bank-accounts');
        }

        try {
            $this->connectBankAccounts->create($user, $ref);

            session()->put('bank-account-success', 'Bank account connected successfully!');

            return redirect()->route('app.bank-accounts');
        } catch (CustomAppException) {
            session()->put('bank-account-error', 'Something went wrong with connecting bank account!');

            return redirect()->route('app.bank-accounts');
        }
    }
}
