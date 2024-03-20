<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserKrakenAccountForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Mary\Traits\Toast;

class AddUserKrakenAccount extends Component
{
    use Toast;

    public UserKrakenAccountForm $form;

    public function create(): void
    {
        $this->form->store();

        $this->success('Kraken account added!', redirectTo: route('app.kraken-accounts'));
    }

    public function render(): View
    {
        return view('livewire.user-kraken-account.add-user-kraken-account');
    }
}
