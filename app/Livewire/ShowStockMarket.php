<?php

declare(strict_types=1);

namespace App\Livewire;

use App\MarketData\Models\UserStockMarket;
use App\Traits\SessionInteraction;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ShowStockMarket extends Component
{
    use Toast;
    use WithPagination;

    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];


    public function delete(UserStockMarket $stockMarket): void
    {
        $stockMarket->delete();

        $this->dispatch('user-stock-market-deleted');
        $this->success('Stock Market deleted successfully');
    }


    public function with(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'ticker', 'label' => 'Stock Name'],
            ['key' => 'price_cents', 'label' => 'Stock Price'],
            ['key' => 'amount', 'label' => 'Stock Quantity'],
        ];

        $rows = UserStockMarket::orderBy(...array_values($this->sortBy))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }


    #[On('user-stock-market-deleted')]
    public function render(): View
    {
        return view('livewire.user-stock-market.show-stock-market', $this->with());
    }
}
