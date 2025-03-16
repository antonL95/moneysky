<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\StockMarket\CreateStockMarket;
use App\Actions\StockMarket\UpdateStockMarket;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\StockMarket\StockMarketData;
use App\Data\App\StockMarket\UserStockMarketData;
use App\Enums\FlashMessageAction;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserStockMarket;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class UserStockMarketController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function index(): Response|RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('viewAny', UserStockMarket::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $stockMarkets = $user->userStockMarket()->get();

        $rows = $stockMarkets->map(
            fn (UserStockMarket $userStockMarket): UserStockMarketData => new UserStockMarketData(
                $userStockMarket->id,
                $userStockMarket->ticker,
                $userStockMarket->amount,
                $userStockMarket->balance,
            ),
        );

        return Inertia::render('stock-market/index', [
            'columns' => [
                'Id',
                'Ticker',
                'Amount',
                'Balance',
            ],
            'rows' => $rows,
        ]);
    }

    public function store(StockMarketData $data, CreateStockMarket $createStockMarket): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('create', UserStockMarket::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $createStockMarket->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(StockMarketData $data, UserStockMarket $stockMarket, UpdateStockMarket $updateStockMarket): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('update', $stockMarket);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }

        $updateStockMarket->handle($stockMarket, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserStockMarket $stockMarket): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('delete', $stockMarket);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }

        $stockMarket->delete();
        ProcessSnapshotJob::dispatch($user);

        return $this->success(FlashMessageAction::DELETE);
    }
}
