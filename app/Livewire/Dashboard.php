<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Currency\ConvertCurrency;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserTransaction;
use App\Crypto\Models\UserCryptoWallets;
use App\Crypto\Models\UserKrakenAccount;
use App\MarketData\Models\UserStockMarket;
use App\Models\User;
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

    protected ?User $user;

    public ?float $bankAccountsSum = null;
    public ?float $cryptoSum = null;
    public ?float $cashWalletsSum = null;
    public ?float $stocksSum = null;

    /**
     * @var string[]
     */
    public array $sortBy = ['column' => 'booked_at', 'direction' => 'desc'];


    public function mount(ConvertCurrency $convertCurrency): void
    {
        $this->convertCurrency = $convertCurrency;

        $this->user = auth()->user();

        if ($this->user === null) {
            $this->redirect(route('login'));
        }
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
}
