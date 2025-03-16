<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\Dashboard\AssetData;
use App\Data\App\Dashboard\TagData;
use App\Data\App\ManualEntry\UserManualEntryData;
use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Models\UserManualEntry;
use App\Services\AssetsService;
use App\Services\BudgetsService;
use App\Services\TransactionsService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController
{
    use HasRedirectWithFlashMessage;

    public function index(
        Request $request,
        AssetsService $assetsService,
        BudgetsService $budgetsService,
        TransactionsService $transactionsService,
    ): Response|RedirectResponse {
        $user = $request->user();

        // @codeCoverageIgnoreStart
        if ($user === null) {
            return redirect()->route('login');
        }

        // @codeCoverageIgnoreEnd

        $request->validate([
            'tagId' => 'nullable|exists:transaction_tags,id',
        ]);

        $tagId = TransactionTag::where('id', $request->get('tagId'))->first();

        $date = null;
        if ($request->has('date')) {
            $date = $request->string('date')->value();
        }

        return Inertia::render('dashboard/index', [
            'totalAssets' => Inertia::defer(static fn (): AssetData => $assetsService->getTotalAsset($user)),
            'assets' => Inertia::defer(static fn (): array => $assetsService->getAssets($user)),
            'historicalAssets' => Inertia::defer(static fn (): array => $assetsService->getHistoricalData($user)),
            'budgets' => Inertia::defer(static fn (): array => $budgetsService->getBudgets($user, $date)->toArray()),
            'activeTab' => $request->get('activeTab', 'investments'),
            'tags' => Inertia::defer(
                static fn (): array => TransactionTag::all()->map(
                    static fn (TransactionTag $tag): TagData => new TagData(
                        $tag->id, $tag->tag,
                    ),
                )->toArray(),
            ),
            'currencies' => CurrencyHelper::getCurrencies(),
            'transactionAggregates' => Inertia::defer(static fn (): array => $transactionsService->getTransactionAggregates($user, $date)),
            'transactions' => Inertia::optional(static fn (): array => $transactionsService->getTransactions($tagId, $date)),
            'userManualEntries' => Inertia::defer(
                static fn () => $user->userManualEntry->map(
                    fn (UserManualEntry $userManualEntry): UserManualEntryData => new UserManualEntryData(
                        $userManualEntry->id,
                        $userManualEntry->name,
                        $userManualEntry->description,
                        $userManualEntry->balance,
                        $userManualEntry->balance_numeric,
                        $userManualEntry->currency,
                    ),
                )->toArray(),
            ),
            'historicalDates' => $transactionsService->getHistoricalDates($user),
            'selectedDate' => $date ?? CarbonImmutable::now()->format('m-Y'),
        ]);
    }
}
