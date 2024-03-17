<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\SessionMessage;
use App\Livewire\Forms\UserStockMarketForm;
use App\MarketData\Models\UserStockMarket;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpdateUserStockMarket extends Component
{
    public UserStockMarketForm $form;

    public UserStockMarket $ticker;

    public function mount(UserStockMarket $ticker): void
    {
        $this->form->ticker = $ticker->ticker;
        $this->form->amount = $ticker->amount;
    }

    public function update(UserStockMarket $userStockMarket): void
    {
        $this->form->update($userStockMarket);

        session()->put(SessionMessage::SUCCESS->value, 'Stock Market updated!');

        $this->redirect(route('app.stock-market'), true);
    }

    public function render(): View
    {
        return view('livewire.update-user-stock-market');
    }
}
