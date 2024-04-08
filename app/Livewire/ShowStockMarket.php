<?php

declare(strict_types=1);

namespace App\Livewire;

use App\MarketData\Models\UserStockMarket;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ShowStockMarket extends Component
{
    use Interactions;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sort = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserStockMarket $stockMarket): void
    {
        $stockMarket->delete();

        $this->dispatch('ticker-deleted');
        $this->toast()->success('Ticker deleted!', 'Ticker deleted successfully!');
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserStockMarket>>
     */
    #[On('ticker-deleted')]
    #[On('ticker-added')]
    #[On('ticker-updated')]
    public function with(): array
    {
        $headers = [
            ['index' => 'id', 'label' => '#'],
            ['index' => 'ticker', 'label' => 'Stock Name'],
            ['index' => 'price_cents', 'label' => 'Stock Price'],
            ['index' => 'amount', 'label' => 'Stock Quantity'],
            ['index' => 'action'],
        ];

        $rows = UserStockMarket::orderBy(...array_values($this->sort))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('ticker-added')]
    public function added(): void
    {
        $this->toast()->success('Ticker added!', 'Ticker added successfully!')->send();
    }

    #[On('ticker-updated')]
    public function updated(): void
    {
        $this->toast()->success('Ticker updated!', 'Ticker updated successfully!')->send();
    }

    public function render(): View
    {
        return view('livewire.user-stock-market.show-stock-market', $this->with());
    }
}
