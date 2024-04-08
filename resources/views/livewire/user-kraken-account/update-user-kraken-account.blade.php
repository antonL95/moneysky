<form wire:submit="update({{$account->id}})">
    <x-ts-input label="{{__('Api Key')}}" wire:model="form.api_key" />
    <x-ts-input label="{{__('Private key')}}" wire:model="form.private_key" type="password" />

    <x-button type="submit" class="mt-2" :title="__('Save')"/>
</form>
