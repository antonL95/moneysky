@php use App\Actions\Currency\ConvertCurrency;use Money\Currency;use Money\Money; @endphp
@props(['amount', 'amountCurrency', 'userCurrency'])
<span>
{{Number::currency(
    (float) (new ConvertCurrency())->convert(
        new Money((int) $amount, new Currency($amountCurrency)),
        new Currency($userCurrency),
    )->getAmount() / 100,
    $userCurrency,
)}}
</span>
