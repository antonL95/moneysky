<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Currency\ConvertCurrency;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserTransaction;
use App\Crypto\Models\UserCryptoWallets;
use App\Crypto\Models\UserKrakenAccount;
use App\MarketData\Models\UserStockMarket;
use App\Models\UserManualEntry;
use App\Models\UserSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Money\Currency;
use Money\Money;

class Dashboard extends Component
{
    use WithPagination;

    protected ConvertCurrency $convertCurrency;

    /**
     * @var string[]
     */
    public array $sortBy = ['column' => 'booked_at', 'direction' => 'desc'];

    /**
     * @var array<string, array<string, array<int, array<string, array<int,int|string>>|string>>|string>
     */
    public array $netWorthChart = [
        'type' => 'doughnut',
        'data' => [
            'labels' => ['Bank', 'Crypto', 'Cash wallets', 'Stocks'],
            'datasets' => [
                [
                    'data' => [300, 50, 100],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BCA81'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BCA81'],
                ],
            ],
        ],
    ];

    public function mount(ConvertCurrency $convertCurrency): void
    {
        $this->convertCurrency = $convertCurrency;

        $user = auth()->user();

        if ($user === null) {
            $this->redirect(route('login'));

            return;
        }

        $backAccountsSum = UserBankAccount::getSumOfAllUserBankAccounts($user);

        if (!is_numeric($backAccountsSum)) {
            $backAccountsSum = 0;
        }

        $backAccountsSum /= 100;

        $cryptoSum = UserCryptoWallets::sum('balance_cents') + UserKrakenAccount::sum('balance_cents');

        if (!is_numeric($cryptoSum)) {
            $cryptoSum = 0;
        }

        $cryptoSum = $this->convertCurrency->convert(
            new Money((int) $cryptoSum, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();

        if (!is_numeric($cryptoSum)) {
            $cryptoSum = 0;
        }

        $cryptoSum /= 100;

        $cashWalletsSum = UserManualEntry::getSumWithCurrency($user);

        if (!is_numeric($cashWalletsSum)) {
            $cashWalletsSum = 0;
        }

        $cashWalletsSum /= 100;

        $stocksSum = UserStockMarket::sum(new Expression('amount * price_cents'));

        if (!is_numeric($stocksSum)) {
            $stocksSum = 0;
        }

        $stocksSum = $this->convertCurrency->convert(
            new Money((int) $stocksSum, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();

        if (!is_numeric($stocksSum)) {
            $stocksSum = 0;
        }

        $stocksSum /= 100;

        Arr::set($this->netWorthChart, 'data.datasets.0.data', [$backAccountsSum, $cryptoSum, $cashWalletsSum, $stocksSum]);
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserTransaction>>
     */
    public function with(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#', 'sort_by'],
            ['key' => 'userBankAccount.name', 'label' => 'Bank account', 'sortable' => false, 'class' => 'hidden md:table-cell'],
            ['key' => 'balance_cents', 'label' => 'Balance'],
            ['key' => 'tag', 'label' => 'Tag', 'sortable' => false],
            ['key' => 'description', 'label' => 'Description', 'sortable' => false, 'class' => 'hidden md:table-cell'],
            ['key' => 'booked_at', 'label' => 'Booked at'],
        ];

        $rows = UserTransaction::with('userBankAccount')
            ->with('transactionTag')
            ->with('userTransactionTag')
            ->orderBy(...array_values($this->sortBy))
            ->paginate(20);

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
}
