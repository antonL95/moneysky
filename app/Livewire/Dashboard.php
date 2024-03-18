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
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Number;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Money\Currency;
use Money\Money;

class Dashboard extends Component
{
    use WithPagination;

    public bool $dark = false;

    protected ConvertCurrency $convertCurrency;

    public function boot(ConvertCurrency $convertCurrency): void
    {
        $this->convertCurrency = $convertCurrency;
    }

    #[On('themeToggle')]
    public function themeToggle(?bool $darkTheme): void
    {
        if ($darkTheme !== null) {
            $this->dark = $darkTheme;
        }
    }

    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.dashboard', [
            'transactions' => UserTransaction::with('userBankAccount')
                ->with('transactionTag')
                ->with('userTransactionTag')
                ->orderBy('booked_at', 'DESC')
                ->paginate(20),
            'pieChartModel' => $this->getChart(),
        ]);
    }

    #[On('currency-updated')]
    public function getChart(): ?PieChartModel
    {
        $user = auth()->user();

        if ($user === null) {
            $this->redirect(route('login'));

            return null;
        }

        $cryptoWalletsSum = UserCryptoWallets::sum('balance_cents') + UserKrakenAccount::sum('balance_cents');
        $bankAccountsSum = UserBankAccount::getSumOfAllUserBankAccounts($user);
        $manualEntriesSum = UserManualEntry::getSumWithCurrency($user);
        $stockMarketSum = UserStockMarket::sum(new Expression('price_cents * amount'));

        if (!is_numeric($cryptoWalletsSum)) {
            $cryptoWalletsSum = 0;
        }

        $cryptoWalletsSum = $this->convertCurrency->convert(
            new Money((int) $cryptoWalletsSum, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount() / 100;

        if (!is_numeric($bankAccountsSum)) {
            $bankAccountsSum = 0;
        }

        $bankAccountsSum /= 100;

        if (!is_numeric($stockMarketSum)) {
            $stockMarketSum = 0;
        }

        $stockMarketSum = $this->convertCurrency->convert(
            new Money((int) $stockMarketSum, new Currency('USD')),
            new Currency(UserSetting::getCurrencyWithDefault()),
        )->getAmount();

        $stockMarketSum /= 100;

        if (!is_numeric($manualEntriesSum)) {
            $manualEntriesSum = 0;
        }

        $manualEntriesSum /= 100;

        $totalSum = $cryptoWalletsSum + $bankAccountsSum + $stockMarketSum + $manualEntriesSum;

        return LivewireCharts::pieChartModel()
            ->asDonut()
            ->addSlice('Crypto', $cryptoWalletsSum, '#38c172')
            ->addSlice('Bank accounts', $bankAccountsSum, '#3490dc')
            ->addSlice('Stock market', $stockMarketSum, '#6574cd')
            ->addSlice('Cash wallet', $manualEntriesSum, '#9f7aea')
            ->setJsonConfig([
                'plotOptions.pie.donut.labels.show' => true,
                'plotOptions.pie.donut.labels.total.showAlways' => true,
                'plotOptions.pie.donut.labels.total.show' => true,
                'plotOptions.pie.donut.labels.total.label' => ['Net Worth'],
                'plotOptions.pie.donut.labels.total.formatter' => '() => `'.Number::forHumans(
                    $totalSum,
                    abbreviate: true,
                ).'`',
            ]);
    }
}
