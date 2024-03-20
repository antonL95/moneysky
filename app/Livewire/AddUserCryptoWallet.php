<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Enums\ChainType;
use App\Livewire\Forms\UserCryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Mary\Traits\Toast;

class AddUserCryptoWallet extends Component
{
    use Toast;

    public UserCryptoWalletForm $form;
    public array $chainTypes;


    public function mount(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        $chainTypes = [];

        foreach (ChainType::cases() as $chainType) {
            $chainTypes[] = [
                'id' => $chainType->value,
                'name' => $chainType->value,
            ];
        }

        $this->chainTypes = $chainTypes;
    }


    public function create(): void
    {
        $this->form->store();

        $this->success('Crypto wallet added successfully.', redirectTo: route('app.crypto-wallets'));
    }


    public function render(): View
    {
        return view('livewire.user-crypto-wallets.add-user-crypto-wallet');
    }
}
