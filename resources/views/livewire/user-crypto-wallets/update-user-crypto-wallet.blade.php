<div>
    <x-form-section submit="update({{$wallet->id}})">
        <x-slot name="title">Crypto Wallet</x-slot>
        <x-slot name="description">Update your crypto wallet</x-slot>

        <x-slot name="form">
            <x-mary-input label="{{__('Wallet address')}}" wire:model="form.wallet_address" inline/>
            <x-mary-select label="{{__('Chain type')}}" wire:model="form.chain_type" :options="$chainTypes"
                           placeholder="Select an chain"
                           placeholder-value="0"/>
        </x-slot>

        <x-slot name="actions">
            <x-mary-button type="submit" class="btn btn-primary">
                {{ __('Update') }}
            </x-mary-button>
        </x-slot>
    </x-form-section>
</div>
