<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserStockMarketForm;
use App\MarketData\Models\UserStockMarket;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class AddUserStockMarket extends Component
{
    use Interactions;

    public UserStockMarketForm $form;

    public function create(): void
    {
        $user = auth()->user();

        if ($user === null || !$user->canAddAdditionalResource(UserStockMarket::class)) {
            $this->toast()->error(
                'Cannot add ticker',
                'You need to upgrade your subscription in order to add another ticker.'
            )->send();

            $this->dispatch('close');

            return;
        }

        $this->form->store();

        $this->dispatch('ticker-added');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-stock-market.add-user-stock-market');
    }
}
