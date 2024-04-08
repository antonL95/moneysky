@php use App\Crypto\Enums\ChainType; @endphp
<form class="max-w-sm mx-auto" wire:submit="update({{$wallet->id}})">
    <x-ts-input label="{{__('Wallet address')}}" wire:model="form.wallet_address"/>
    <x-ts-select.styled
        label="{{__('Chain type')}}"
        wire:model="form.chain_type"
        :options="ChainType::cases()"
    />
    <x-button type="submit" :title="__('Save')" class="mt-2"/>
</form>
