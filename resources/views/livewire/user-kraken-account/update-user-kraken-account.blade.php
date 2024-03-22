<div>
    <x-form-section submit="update({{$account->id}})">
        <x-slot name="title">Kraken account</x-slot>
        <x-slot name="description">Update your kraken account</x-slot>

        <x-slot name="form" autocomplete="off">
            <x-mary-input label="{{__('Api Key')}}" wire:model="form.api_key" inline />
            <x-mary-input label="{{__('Private key')}}" wire:model="form.private_key" inline type="password" autocomplete="off"/>
        </x-slot>

        <x-slot name="actions">
            <x-mary-button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </x-mary-button>
        </x-slot>
    </x-form-section>
</div>
