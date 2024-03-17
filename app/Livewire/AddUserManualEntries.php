<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserManualEntryForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddUserManualEntries extends Component
{
    public UserManualEntryForm $form;

    public function create(): void
    {
        $this->form->store();

        $this->dispatch('userManualEntryAdded');
        $this->redirect(route('app.manual-entries'), true);
    }

    public function render(): View
    {
        return view('livewire.add-user-manual-entries');
    }
}
