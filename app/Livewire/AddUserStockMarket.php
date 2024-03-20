<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\SessionMessage;
use App\Livewire\Forms\UserStockMarketForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Mary\Traits\Toast;

class AddUserStockMarket extends Component
{
    use Toast;

    public UserStockMarketForm $form;

    public function create(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        $this->form->store();

        $this->success('Stock Market added!', redirectTo: route('app.stock-market'));
    }

    public function render(): View
    {
        return view('livewire.user-stock-market.add-user-stock-market');
    }
}
