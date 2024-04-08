<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Exceptions\CustomAppException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Laravel\Cashier\Cashier;
use Livewire\Component;

class PriceTable extends Component
{
    public string $plusPrice = '179';

    public string $unlimitedPrice = '249';

    public function mount(): void
    {
        $prices = $this->getAndFormatPrices();

        $this->plusPrice = $prices['plus_price'];
        $this->unlimitedPrice = $prices['unlimited_price'];
    }

    public function render(): View
    {
        return view('livewire.price-table');
    }

    /**
     * @return array<string, string>
     */
    private function getAndFormatPrices(): array
    {
        return Cache::remember('price-table', 24 * 60 * 60, static function () {
            $plusSubscriptionId = Config::get('services.stripe.plus_plan_id');
            $unlimitedSubscriptionId = Config::get('services.stripe.unlimited_plan_id');

            if (!\is_string($plusSubscriptionId) || !\is_string($unlimitedSubscriptionId)) {
                throw CustomAppException::invalidConfig();
            }

            $plusSubscriptionPrice = Cashier::stripe()->prices->retrieve($plusSubscriptionId);
            $unlimitedSubscriptionPrice = Cashier::stripe()->prices->retrieve($unlimitedSubscriptionId);

            return [
                'plus_price' => (string) round($plusSubscriptionPrice->unit_amount / 100),
                'unlimited_price' => (string) round($unlimitedSubscriptionPrice->unit_amount / 100),
            ];
        });
    }
}
