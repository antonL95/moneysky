<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Currency\ConvertCurrency;
use App\Bank\Models\TransactionTag;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserTransaction;
use App\Crypto\Models\UserCryptoWallets;
use App\Crypto\Models\UserKrakenAccount;
use App\ManualEntry\Models\UserManualEntry;
use App\MarketData\Models\UserStockMarket;
use App\Models\User;
use App\UserSetting\Models\UserSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Money\Currency;
use Money\Money;

class Dashboard extends Component
{
    use WithPagination;

    protected ConvertCurrency $convertCurrency;

    protected User $user;

    public ?float $bankAccountsSum = null;

    public ?float $cryptoSum = null;

    public ?float $cashWalletsSum = null;

    public ?float $stocksSum = null;

    public array $monthlyExpenses = [];

    /**
     * @var string[]
     */
    public array $sort = ['column' => 'booked_at', 'direction' => 'desc'];

    public function mount(ConvertCurrency $convertCurrency): void
    {
        $this->convertCurrency = $convertCurrency;

        $user = auth()->user();

        if ($user === null) {
            $this->redirect(route('login'));

            return;
        }

        $this->user = $user;
        $this->bankAccountSum();
        $this->cryptoSum();
        $this->stockMarketSum();
        $this->cashWalletsSum();
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserTransaction>>
     */
    public function with(): array
    {
        $headers = [
            ['index' => 'userBankAccount.name', 'label' => 'Bank account', 'sortable' => false, 'class' => 'hidden md:table-cell'],
            ['index' => 'balance_cents', 'label' => 'Balance'],
            ['index' => 'tag', 'label' => 'Tag', 'sortable' => false],
            ['index' => 'description', 'label' => 'Description', 'sortable' => false, 'class' => 'hidden md:table-cell'],
            ['index' => 'booked_at', 'label' => 'Booked at'],
        ];

        $rows = UserTransaction::with('userBankAccount')
            ->with('transactionTag')
            ->with('userTransactionTag')
            ->orderBy(...array_values($this->sort))
            ->paginate(20);

        $this->sumOfMonthlyExpenses();

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.dashboard', $this->with());
    }

    public function bankAccountSum(): void
    {
        $sum = UserBankAccount::getSumOfAllUserBankAccounts($this->user);

        if ($sum === 0) {
            return;
        }

        $this->bankAccountsSum = $sum;
    }

    public function cryptoSum(): void
    {
        $cryptoSum = UserCryptoWallets::sum('balance_cents') + UserKrakenAccount::sum('balance_cents');

        if (!is_numeric($cryptoSum)) {
            $cryptoSum = 0;
        }

        if ($cryptoSum === 0) {
            return;
        }

        $this->cryptoSum = (float) $this->convertCurrency->convert(
            new Money((int) $cryptoSum, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();
    }

    public function stockMarketSum(): void
    {
        $stocksSum = UserStockMarket::sum(new Expression('amount * price_cents'));

        if (!is_numeric($stocksSum)) {
            $stocksSum = 0;
        }

        if ($stocksSum === 0) {
            return;
        }

        $this->stocksSum = (float) $this->convertCurrency->convert(
            new Money((int) $stocksSum, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();
    }

    public function cashWalletsSum(): void
    {
        $sum = UserManualEntry::getSumWithCurrency($this->user);
        if ($sum === 0) {
            return;
        }
        $this->cashWalletsSum = $sum;
    }

    public function sumOfMonthlyExpenses(): void
    {
        $now = Carbon::now();
        $lastMonth = $now->subMonth()->startOfMonth();
        $currentMonth = UserTransaction::with('transactionTag')->where('booked_at', '>=', $now->startOfMonth()->toDateString())->get();
        $previousMonth = UserTransaction::with('transactionTag')->whereBetween('booked_at', [$lastMonth->toDateString(), $lastMonth->endOfMonth()->toDateString()])->get();

        $streamingSumCurrentMonth = 0;
        $subscriptionsSumCurrentMonth = 0;
        $streamingSumPreviousMonth = 0;
        $subscriptionsSumPreviousMonth = 0;

        $this->analyzeTransactions($currentMonth, 5, $streamingSumCurrentMonth);
        $this->analyzeTransactions($currentMonth, 6, $subscriptionsSumCurrentMonth);
        $this->analyzeTransactions($previousMonth, 5, $streamingSumPreviousMonth);
        $this->analyzeTransactions($previousMonth, 6, $subscriptionsSumPreviousMonth);

        $this->monthlyExpenses = [
            'currentMonth' => [
                'subscriptions' => $subscriptionsSumCurrentMonth,
                'streaming' => $streamingSumCurrentMonth,
            ],
            'previousMonth' => [
                'subscriptions' => $subscriptionsSumPreviousMonth,
                'streaming' => $streamingSumPreviousMonth,
            ],
        ];
    }


    /**
     * @param Collection<UserTransaction> $collection
     */
    private function analyzeTransactions(Collection $collection, int $tagId, int &$sum): void
    {
        foreach ($collection as $item) {
            if ($item->transactionTag->id === $tagId) {
                $sum += (int) $this->convertCurrency->convert(
                    new Money($item->balance_cents, new Currency($item->currency)),
                    new Currency(UserSetting::getCurrencyWithDefault()),
                )->getAmount();
            }
        }
    }
}
