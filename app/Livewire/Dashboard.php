<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Currency\ConvertCurrency;
use App\Bank\Models\UserTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    protected ConvertCurrency $convertCurrency;

    public array $sortBy = ['column' => 'booked_at', 'direction' => 'desc'];

    public array $netWorthChart = [
        'type' => 'doughnut',
        'data' => [
            'labels' => ['Bank', 'Cash wallets', 'Crypto', 'Stocks'],
            'datasets' => [
                [
                    'data' => [300, 50, 100],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#FF6384'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#FF6384'],
                ],
            ],
        ],
    ];


    public function mount(ConvertCurrency $convertCurrency): void
    {
        $this->convertCurrency = $convertCurrency;
    }

    public function boot(): void
    {

        Arr::set($this->netWorthChart, 'data.datasets.0.data', [300, 50, 100]);
    }


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
