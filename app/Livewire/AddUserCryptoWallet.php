<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Enums\ChainType;
use App\Crypto\Models\UserCryptoWallets;
use App\Livewire\Forms\UserCryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class AddUserCryptoWallet extends Component
{
    use Interactions;

    public UserCryptoWalletForm $form;

    /**
     * @var string[][]
     */
    public array $chainTypes;

    public function mount(): void
    {
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
        $user = auth()->user();

        if ($user === null || !$user->canAddAdditionalResource(UserCryptoWallets::class)) {
            $this->toast()->error(
                'Cannot add wallet',
                'You need to upgrade your subscription in order to add another wallet.'
            )->send();

            $this->dispatch('close');

            return;
        }

        $this->form->store();

        $this->dispatch('crypto-added');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-crypto-wallets.add-user-crypto-wallet');
    }
}
