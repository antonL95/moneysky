@php use Money\Currencies\ISOCurrencies; @endphp
<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="update({{$wallet}})">
            <x-slot name="title">Cash wallet</x-slot>
            <x-slot name="description">Update cash wallet</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-input label="{{__('Name')}}" wire:model="form.name"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-number label="{{__('Amount')}}" wire:model="form.amount"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-textarea label="{{__('Description')}}" wire:model="form.description"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-select.styled
                        :options="array_map(
                            static fn (string $currency) => ['label' => $currency, 'value' => $currency],
                            (array) (new ISOCurrencies)->getIterator()
                        )"
                        select="label:label|value:value"
                        searchable wire:model="form.currency" required/>
                </div>
            </x-slot>

            <x-slot name="actions">
                <x-button>
                    {{ __('Save') }}
                </x-button>
            </x-slot>
        </x-form-section>
    </main>
</div>
