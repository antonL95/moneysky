@php use App\Crypto\Enums\ChainType; @endphp
<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="update({{$wallet->id}})">
            <x-slot name="title">Crypto Wallet</x-slot>
            <x-slot name="description">Manage your crypto wallet</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="walletAddress" value="{{ __('Address') }}"/>
                    <x-input id="walletAddress" type="text" wire:model="form.wallet_address"/>
                    <x-input-error for="form.walletAddress"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-select.styled
                        :options="ChainType::cases()"
                        select="label:label|value:value"
                        searchable wire:model="form.chain_type" required/>
                    <x-input-error for="form.chain"/>
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
