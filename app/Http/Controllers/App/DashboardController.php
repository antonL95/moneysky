<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\Dashboard\AssetData;
use App\Data\App\Dashboard\TagData;
use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Services\AssetsService;
use App\Services\BudgetsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController
{
    use HasRedirectWithFlashMessage;

    public function index(Request $request, AssetsService $assetsService, BudgetsService $budgetsService): Response|RedirectResponse
    {
        $user = $request->user();

        // @codeCoverageIgnoreStart
        if ($user === null) {
            return redirect()->route('login');
        }

        // @codeCoverageIgnoreEnd

        return Inertia::render('dashboard/index', [
            'totalAssets' => Inertia::defer(static fn (): AssetData => $assetsService->getTotalAsset($user)),
            'assets' => Inertia::defer(static fn (): array => $assetsService->getAssets($user)),
            'historicalAssets' => Inertia::defer(static fn (): array => $assetsService->getHistoricalData($user)),
            'budgets' => Inertia::defer(static fn (): array => $budgetsService->getBudgets($user, null)->toArray()),
            'activeTab' => $request->get('activeTab', 'investments'),
            'tags' => Inertia::defer(
                static fn (): array => TransactionTag::all()->map(
                    static fn (TransactionTag $tag): TagData => new TagData(
                        $tag->id, $tag->tag,
                    ),
                )->toArray(),
            ),
            'currencies' => CurrencyHelper::getCurrencies(),
        ]);
    }
}
