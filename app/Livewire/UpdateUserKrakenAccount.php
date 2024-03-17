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
        $this->form->apiKey = $account->api_key;
        $this->form->privateKey = $account->private_key;
    }

    public function update(UserKrakenAccount $account): void
    {
        $this->form->update($account);

        $this->dispatch('userKrakenAccountUpdated');

        $this->toast()->success('Kraken account updated!')->send();

        $this->redirect(route('app.kraken-accounts'), true);
    }

    public function render(): View
    {
        return view('livewire.update-user-kraken-account');
    }
}
