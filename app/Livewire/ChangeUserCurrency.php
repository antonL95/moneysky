<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\CacheKeys;
use App\Enums\UserSettingKeys;
use App\Models\UserSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Mary\Traits\Toast;

class ChangeUserCurrency extends Component
{
    use Toast;

    public string $currency;

    public function mount(): void
    {
        $this->currency = UserSetting::getCurrencyWithDefault();
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

        $this->success('Currency updated successfully');
        $this->dispatch('currency-updated');
    }

    public function render(): View
    {
        return view('livewire.change-user-currency');
    }
}
