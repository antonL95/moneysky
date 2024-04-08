<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserManualEntryForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use TallStackUi\Traits\Interactions;

class AddUserManualEntries extends Component
{
    use Interactions;

    public UserManualEntryForm $form;

    /**
     * @var string[][]
     */
    public array $currencies = [];

    public function mount(): void
    {
        $currencies = [];

        foreach ((new ISOCurrencies)->getIterator() as $currency) {
            $currencies[] = [
                'id' => $currency->getCode(),
                'name' => $currency->getCode(),
            ];
        }

        $this->currencies = $currencies;
    }

    public function create(): void
    {
        $this->form->store();

        $this->dispatch('cash-added');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-manual-entries.add-user-manual-entries');
    }
}
