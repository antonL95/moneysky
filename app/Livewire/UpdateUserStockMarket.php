<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserStockMarketForm;
use App\Models\UserStockMarket;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class UpdateUserStockMarket extends Component
{
    public UserStockMarketForm $form;

    public UserStockMarket $ticker;

    use Interactions;

    public function mount(UserStockMarket $ticker): void
    {
        $this->form->ticker = $ticker->ticker;
        $this->form->amount = $ticker->amount;
    }

    public function update(UserStockMarket $userStockMarket): void
    {
        $this->form->update($userStockMarket);

        $this->dispatch('ticker-updated');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-stock-market.update-user-stock-market');
    }
}
