@php use App\Crypto\Enums\ChainType; @endphp
<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="create">
            <x-slot name="title">Crypto Wallet</x-slot>
            <x-slot name="description">Add your crypto wallet</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-input label="{{__('Wallet address')}}" wire:model="form.wallet_address"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-select.styled
                        label="{{__('Chain Type')}}"
                        :options="ChainType::cases()"
                        select="label:label|value:value"
                        searchable wire:model="form.chain_type" required/>
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
