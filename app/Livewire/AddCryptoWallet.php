<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\CryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddCryptoWallet extends Component
{
    public CryptoWalletForm $form;

    public function create(): void
    {
        $this->form->store();

        $this->dispatch('wallet-created');
        $this->redirect(route('app.crypto-wallets'), true);
    }

    public function render(): View
    {
        return view('livewire.add-crypto-wallet');
    }
}
