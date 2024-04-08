<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserManualEntryForm;
use App\ManualEntry\Models\UserManualEntry;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use TallStackUi\Traits\Interactions;

class UpdateUserManualEntries extends Component
{
    use Interactions;

    public UserManualEntryForm $form;

    public UserManualEntry $wallet;

    /**
     * @var array<int, array{id: string, name: string}>
     */
    public array $currencies = [];

    public function mount(UserManualEntry $wallet): void
    {
        $this->form->name = $wallet->name;
        $this->form->amount = $wallet->amount_cents / 100;
        $this->form->description = $wallet->description;
        $this->form->currency = $wallet->currency;
        $currencies = [];

        foreach ((new ISOCurrencies)->getIterator() as $currency) {
            $currencies[] = [
                'id' => $currency->getCode(),
                'name' => $currency->getCode(),
            ];
        }

        $this->currencies = $currencies;
    }

    public function update(UserManualEntry $wallet): void
    {
        $this->form->update($wallet);

        $this->dispatch('cash-updated');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-manual-entries.update-user-manual-entries');
    }
}
