@php
    use App\Actions\Currency\ConvertCurrency;
    use App\Models\UserSetting;
    use Money\Currency;
    use Money\Money;
@endphp

@props([
    'amount',
    'amountCurrency',
])
<span>
    {{
        Number::currency(
            (float) app(ConvertCurrency::class)
                ->convert(
                    new Money((int) $amount, new Currency($amountCurrency ?? UserSetting::getCurrencyWithDefault())),
                    new Currency(UserSetting::getCurrencyWithDefault()),
                )
                ->getAmount() / 100,
            UserSetting::getCurrencyWithDefault(),
        )
    }}
</span>
