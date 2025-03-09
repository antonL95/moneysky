<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Helpers\CurrencyHelper;
use App\Services\ConvertCurrencyService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Money\Currency;
use Money\Money;

trait HasBalanceAttribute
{
    /**
     * @return Attribute<string, never>
     */
    protected function balance(): Attribute
    {
        $user = Auth::user();

        if ($user === null) {
            return Attribute::make(
                get: static fn (): string => (string) Number::currency(0, CurrencyHelper::defaultCurrency()),
            );
        }

        $balance = $this->balance_numeric;

        return Attribute::make(
            get: static fn (): string => (string) Number::currency($balance, $user->currency),
        );
    }

    /**
     * @return Attribute<float, never>
     */
    protected function balanceNumeric(): Attribute
    {
        $user = Auth::user();

        if ($user === null) {
            return Attribute::make(
                get: static fn (): float => 0.0,
            );
        }

        if ($this->balance_cents === null) {
            return Attribute::make(
                get: static fn (): float => 0.0,
            );
        }

        /** @var non-empty-string $modelCurrency */
        $modelCurrency = $this->currency ?? CurrencyHelper::defaultCurrency(); // @phpstan-ignore-line

        $currencyConvertor = new ConvertCurrencyService;

        /** @var non-empty-string $userCurrency */
        $userCurrency = $user->currency;

        $balance = (int) $currencyConvertor->convert(
            new Money((int) $this->balance_cents, new Currency($modelCurrency)),
            new Currency($userCurrency),
        )->getAmount();

        return Attribute::make(
            get: static fn (): float => $balance / 100,
        );
    }
}
