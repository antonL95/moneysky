<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Livewire\Component;

class PricingTable extends Component
{
    public string $amount = '9.99';

    public string $currency = 'USD';

    /** @var array<int, array<string, string>> */
    public array $currencies = [
        ['id' => 'USD', 'name' => 'USD'],
        ['id' => 'EUR', 'name' => 'EUR'],
        ['id' => 'CZK', 'name' => 'CZK'],
    ];

    public function mount(): void
    {
        $prices = $this->getPrices();

        $this->amount = (string) Number::currency(
            round($prices[$this->currency] / 100, 2),
            $this->currency,
        );
    }

    public function render(): View
    {
        $prices = $this->getPrices();
        $this->amount = (string) Number::currency(
            round($prices[$this->currency] / 100, 2),
            $this->currency,
        );

        return view('livewire.pricing-table');
    }

    /**
     * @return array<string, int>
     */
    private function getPrices(): array
    {
        $priceId = config('services.stripe.monthly_plan');

        if (!\is_string($priceId)) {
            return [];
        }

        /** @var array<string, int> $prices */
        $prices = Cache::remember('stripe_price_'.$priceId, 60 * 60 * 24, fn () => $this->fetchPrices($priceId));

        return $prices;
    }

    /**
     * @return array<string, int>
     */
    private function fetchPrices(
        string $priceId,
    ): array {
        $price = Cashier::stripe()->prices->retrieve(
            $priceId,
            [
                'expand' => ['currency_options'],
            ],
        );

        if ($price->currency_options === null) {
            return [];
        }

        $result = [];

        foreach ($price->currency_options->toArray() as $currency => $values) {
            $result[Str::upper($currency)] = $values['unit_amount'];
        }

        return $result;
    }
}
