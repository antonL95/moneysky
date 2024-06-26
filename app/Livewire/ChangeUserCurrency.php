<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\CacheKeys;
use App\Enums\UserSettingKeys;
use App\Models\UserSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Money\Currencies\ISOCurrencies;
use TallStackUi\Traits\Interactions;

class ChangeUserCurrency extends Component
{
    use Interactions;

    public string $currency;

    /**
     * @var string[][]
     */
    public array $currencies = [];

    public function mount(): void
    {
        $this->currency = UserSetting::getCurrencyWithDefault();
        foreach ((new ISOCurrencies)->getIterator() as $currency) {
            $this->currencies[] = [
                'id' => $currency->getCode(),
                'name' => $currency->getCode(),
            ];
        }
    }

    public function updatedCurrency(): void
    {
        UserSetting::updateOrCreate([
            'user_id' => auth()->id(),
            'key' => UserSettingKeys::CURRENCY->value,
        ], [
            'value' => $this->currency,
        ]);

        Cache::forget(CacheKeys::USER_CURRENCY->value.'-'.auth()->id());

        Cache::put(CacheKeys::USER_CURRENCY->value.'-'.auth()->id(), $this->currency);

        $this->toast()->success('Currency updated!', 'Currency updated successfully')->send();

        $this->dispatch('currency-updated');
    }

    public function render(): View
    {
        return view('livewire.change-user-currency');
    }
}
