<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserKrakenAccount;
use App\Livewire\Forms\UserKrakenAccountForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class UpdateUserKrakenAccount extends Component
{
    use Interactions;

    public UserKrakenAccountForm $form;

    public UserKrakenAccount $account;

    public function mount(UserKrakenAccount $account): void
    {
        $this->form->api_key = $account->api_key;
        $this->form->private_key = $account->private_key;
    }

    public function update(UserKrakenAccount $account): void
    {
        $this->form->update($account);

        $this->dispatch('kraken-updated');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-kraken-account.update-user-kraken-account');
    }
}
