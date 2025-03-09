<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Dashboard\CreateTransaction;
use App\Actions\Dashboard\UpdateTransaction;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\TransactionData;
use App\Data\UserTransactionData;
use App\Enums\FlashMessageAction;
use App\Enums\TransactionType;
use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Models\UserManualEntry;
use App\Models\UserTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use TypeError;

final class UserTransactionController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function index(Request $request, ?TransactionTag $transactionTag): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $now = CarbonImmutable::now();

        try {
            if ($request->query('date') !== null) {
                $now = CarbonImmutable::createFromFormat('m-Y', type($request->query('date'))->asString());
            }
        } catch (TypeError) {
        }

        if (! $now instanceof CarbonImmutable) {
            $now = CarbonImmutable::now();
        }

        /** @var Collection<int, UserTransactionData> $transactions */
        $transactions = UserTransaction::where('transaction_tag_id', $transactionTag?->id)
            ->with(['userBankAccount', 'userManualEntry'])
            ->where('booked_at', '>=', $now->startOfMonth())
            ->where('booked_at', '<=', $now->endOfMonth())
            ->get()
            ->map(
                fn (UserTransaction $transaction): UserTransactionData => new UserTransactionData(
                    id: $transaction->id,
                    balance: $transaction->balance,
                    amount: ($transaction->balance_cents / 100) * -1,
                    description: $transaction->description,
                    currency: $transaction->currency,
                    bookedAt: $transaction->booked_at->format('Y-m-d'),
                    userManualEntryId: $transaction->user_manual_entry_id,
                    transactionTagId: $transaction->transaction_tag_id,
                    transactionType: $transaction->user_manual_entry_id !== null ? TransactionType::MANUAL : TransactionType::AUTOMATIC,
                    bankAccountName: $transaction->userBankAccount?->name,
                    cashWalletName: $transaction->userManualEntry?->name,
                ),
            );

        return Inertia::render('Transaction/Index', [
            'transactions' => $transactions,
            'columns' => [
                'Account Name',
                'Balance',
                'Description',
                'Booked At',
            ],
            'title' => $transactionTag->tag ?? 'Other',
            'start' => $now->startOfMonth()->format('d/m/Y'),
            'end' => $now->isCurrentMonth()
                ? $now->format('d/m/Y')
                : $now->endOfMonth()->format('d/m/Y'),
            'dateFilter' => $request->query('date'),
            'currencies' => CurrencyHelper::getCurrencies(),
            'userManualEntries' => $user->userManualEntry()->get()->map(fn (UserManualEntry $userManualEntry): array => [
                'id' => $userManualEntry->id,
                'name' => $userManualEntry->name,
            ])->toArray(),
            'tags' => TransactionTag::all()
                ->map(fn (TransactionTag $tag): array => ['id' => $tag->id, 'name' => $tag->tag])
                ->toArray(),
        ]);
    }

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
