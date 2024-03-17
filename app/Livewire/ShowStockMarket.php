<?php

declare(strict_types=1);

namespace App\Livewire;

use App\MarketData\Models\UserStockMarket;
use App\Traits\SessionInteraction;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ShowStockMarket extends Component
{
    use SessionInteraction;
    use WithPagination;

    public function delete(UserStockMarket $stockMarket): void
    {
        $stockMarket->delete();

        $this->dispatch('userStockMarketDeleted');
        $this->toast()->success('Stock Market deleted successfully')->send();
    }

    #[On('userStockMarketDeleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.show-stock-market', [
            'tickers' => UserStockMarket::paginate(10),
        ]);
    }
}
