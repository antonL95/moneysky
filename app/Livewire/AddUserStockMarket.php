<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\SessionMessage;
use App\Livewire\Forms\UserStockMarketForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddUserStockMarket extends Component
{
    public UserStockMarketForm $form;

    public function create(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        $this->form->store();

        session()->put(SessionMessage::SUCCESS->value, 'Stock Market added successfully!');

        $this->redirect(route('app.stock-market'), true);
    }

    public function render(): View
    {
        return view('livewire.add-user-stock-market');
    }
}
