<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserManualEntryForm;
use App\Models\UserManualEntry;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Mary\Traits\Toast;
use Money\Currencies\ISOCurrencies;

class UpdateUserManualEntries extends Component
{
    use Toast;
    public UserManualEntryForm $form;

    public UserManualEntry $wallet;


    public array $currencies = [];


    public function mount(UserManualEntry $wallet): void
    {
        $this->form->name = $wallet->name;
        $this->form->amount = $wallet->amount_cents / 100;
        $this->form->description = $wallet->description;
        $this->form->currency = $wallet->currency;
        $currencies = [];

        foreach ((new ISOCurrencies())->getIterator() as $currency) {
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

        $this->success('User manual entry updated successfully', redirectTo: route('app.manual-entries'));
    }

    public function render(): View
    {
        return view('livewire.user-manual-entries.update-user-manual-entries');
    }
}
