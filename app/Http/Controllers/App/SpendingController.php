<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Dashboard\Spending;
use App\Data\App\Dashboard\UserBudgetData;
use App\Helpers\CurrencyHelper;
use App\Models\TransactionTag;
use App\Models\UserBudget;
use App\Models\UserManualEntry;
use App\Services\ConvertCurrencyService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use Inertia\Inertia;
use Inertia\Response;
use Money\Currency;
use Money\Money;
use TypeError;

final class SpendingController
{
    public function index(Request $request, Spending $spending, ConvertCurrencyService $convertCurrency): Response|RedirectResponse
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

        $prevMonth = $now->previous('month')->format('m-Y');
        if (! $now->isCurrentMonth()) {
            $nextMonth = $now->next('month')->format('m-Y');
        }

        /** @var Collection<int, UserBudgetData|null> $userBudgets */
        $userBudgets = $user->budgets()
            ->with(
                [ // @phpstan-ignore-line
                    'tags',
                    'userTags',
                    'periods' => function (HasMany $query) use ($now): void {
                        $query->where('start_date', '>=', $now->startOfMonth())
                            ->where('end_date', '<=', $now->endOfMonth());
                    },
                ],
            )->get()->map(
                /** @returns UserBudgetData|null */
                function (UserBudget $userBudget) use ($user, $convertCurrency): ?UserBudgetData {
                    /** @var non-empty-string $budgetCurrency */
                    $budgetCurrency = $userBudget->currency;
                    /** @var non-empty-string $userCurrency */
                    $userCurrency = $user->currency;

                    /** @var int[] $tags */
                    $tags = $userBudget->tags->map(fn (TransactionTag $transactionTag) => $transactionTag->id)->toArray();

                    try {
                        return new UserBudgetData(
                            $userBudget->periods->firstOrFail()->id,
                            $userBudget->name,
                            ((int) $convertCurrency->convert(
                                new Money($userBudget->periods->firstOrFail()->balance_cents, new Currency($budgetCurrency)),
                                new Currency($userCurrency),
                            )->getAmount()) / 100,
                            (float) $userBudget->balance_numeric,
                            $userBudget->currency,
                            $tags,
                            $userBudget->id,
                        );
                    } catch (ItemNotFoundException) {
                        return null;
                    }
                },
            );

        return Inertia::render('Spending/Index', [
            'stats' => fn (): Collection => $spending->handle($user, $now),
            'start' => $now->startOfMonth()->format('d/m/Y'),
            'end' => $now->isCurrentMonth()
                ? $now->format('d/m/Y')
                : $now->endOfMonth()->format('d/m/Y'),
            'currencies' => CurrencyHelper::getCurrencies(),
            'userBudgets' => $userBudgets,
            'userCurrency' => $user->currency,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth ?? null,
            'isCurrentMonth' => $now->isCurrentMonth(),
            'dateFilter' => $request->query('date'),
            'userManualEntries' => $user->userManualEntry()->get()->map(fn (UserManualEntry $userManualEntry): array => [
                'id' => $userManualEntry->id,
                'name' => $userManualEntry->name,
            ])->toArray(),
            'tags' => TransactionTag::all()
                ->map(fn (TransactionTag $tag): array => ['id' => $tag->id, 'name' => $tag->tag])
                ->toArray(),
        ]);
    }
}
