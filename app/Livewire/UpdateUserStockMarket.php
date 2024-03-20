<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserStockMarketForm;
use App\MarketData\Models\UserStockMarket;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Mary\Traits\Toast;

class UpdateUserStockMarket extends Component
{
    public UserStockMarketForm $form;

    public UserStockMarket $ticker;

    use Toast;

    public function mount(UserStockMarket $ticker): void
    {
        $this->form->ticker = $ticker->ticker;
        $this->form->amount = $ticker->amount;
    }

    public function update(UserStockMarket $userStockMarket): void
    {
        $this->form->update($userStockMarket);

        $this->success('Stock Market updated successfully', redirectTo: route('app.stock-market'));
    }

    public function render(): View
    {
        return view('livewire.user-stock-market.update-user-stock-market');
    }
}
