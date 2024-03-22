<x-form-section submit="updatedCurrency">
    <x-slot name="title">
        {{ __('Change default currency') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s account default display currency.') }}
    </x-slot>

    <x-slot name="form">
            <x-mary-choices-offline
                label="{{ __('Currency') }}"
                :options="$currencies"
                wire:model.live="currency"
                single
                searchable
                required/>
    </x-slot>
</x-form-section>
