<div>
    <x-form-section submit="create">
        <x-slot name="title">Stock ticker</x-slot>
        <x-slot name="description">Create ticker with amount</x-slot>

        <x-slot name="form">
                <x-mary-input label="{{__('Ticker')}}" wire:model="form.ticker" inline />
                <x-mary-input label="{{__('Amount')}}" wire:model="form.amount" inline step="0.0001" />
        </x-slot>

        <x-slot name="actions">
            <x-mary-button type="submit">
                {{ __('Save') }}
            </x-mary-button>
        </x-slot>
    </x-form-section>
</div>
