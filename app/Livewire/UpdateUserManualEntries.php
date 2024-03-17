<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserManualEntryForm;
use App\Models\UserManualEntry;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class UpdateUserManualEntries extends Component
{
    public UserManualEntryForm $form;

    public UserManualEntry $wallet;

    use Interactions;

    public function mount(UserManualEntry $wallet): void
    {
        $this->form->name = $wallet->name;
        $this->form->amount = $wallet->amount_cents / 100;
        $this->form->description = $wallet->description;
        $this->form->currency = $wallet->currency;
    }

    public function update(UserManualEntry $wallet): void
    {
        $this->form->update($wallet);

        $this->dispatch('userManualEntryUpdated');
        $this->redirect(route('app.manual-entries'), true);
    }

    public function render(): View
    {
        return view('livewire.update-user-manual-entries');
    }
}
