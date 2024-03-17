@php use Money\Currencies\ISOCurrencies;use App\Models\UserSetting; @endphp
<div>
    <form wire:submit="updatedCurrency">
        <x-ts-select.styled
            :options="array_map(
                            fn (string $currency) => ['label' => $currency, 'value' => $currency, 'selected' => $currency === $this->currency],
                            (array) (new ISOCurrencies)->getIterator()
                        )"
            select="label:label|value:value|selected:selected"
            searchable wire:model.live="currency" required placeholder="{{__('Choose currency')}}"/>
    </form>
</div>

