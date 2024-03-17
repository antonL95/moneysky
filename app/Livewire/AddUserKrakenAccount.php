<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserKrakenAccountForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class AddUserKrakenAccount extends Component
{
    use Interactions;

    public UserKrakenAccountForm $form;

    public function create(): void
    {
        $this->form->store();

        $this->dispatch('userKrakenAccountAdded');
        $this->toast()->success('Kraken account added!')->send();

        $this->redirect(route('app.kraken-accounts'), true);
    }

    public function render(): View
    {
        return view('livewire.add-user-kraken-account');
    }
}
