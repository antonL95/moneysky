@php use Money\Currencies\ISOCurrencies; @endphp
<div>
    <x-form-section submit="update({{$wallet->id}})">
        <x-slot name="title">Cash wallet</x-slot>
        <x-slot name="description">Update cash wallet</x-slot>

        <x-slot name="form">
            <x-mary-input label="{{__('Name')}}" wire:model="form.name" inline/>
            <x-mary-input label="{{__('Amount')}}" money wire:model="form.amount" inline step="0.01"/>
            <x-mary-textarea label="{{__('Description')}}" wire:model="form.description"/>
            <x-mary-choices-offline label="{{__('Currency')}}" wire:model="form.currency"
                                    single
                                    searchable  :options="$currencies"/>
        </x-slot>

        <x-slot name="actions">
            <x-mary-button type="submit">
                {{ __('Save') }}
            </x-mary-button>
        </x-slot>
    </x-form-section>
</div>