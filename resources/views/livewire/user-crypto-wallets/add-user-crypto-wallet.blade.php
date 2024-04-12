@php
    use App\Enums\ChainType;
@endphp

<form class="mx-auto max-w-sm" wire:submit="create">
    <x-ts-input label="{{__('Wallet address')}}" wire:model="form.wallet_address" />
    <x-ts-select.styled label="{{__('Chain type')}}" wire:model="form.chain_type" :options="ChainType::cases()" />
    <x-button type="submit" :title="__('Save')" class="mt-2" />
</form>
