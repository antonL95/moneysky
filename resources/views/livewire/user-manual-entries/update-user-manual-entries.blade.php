<form wire:submit="update({{$wallet->id}})">
    <x-ts-input label="{{__('Name')}}" wire:model="form.name"/>
    <x-ts-number label="{{__('Amount')}}" wire:model="form.amount" step="0.01"/>
    <x-ts-textarea label="{{__('Description')}}" wire:model="form.description"/>
    <x-ts-select.styled label="{{__('Currency')}}"
                        wire:model="form.currency"
                        searchable
                        select="label:name|value:id"
                        :options="$currencies"/>

    <x-button type="submit" class="mt-2" :title="__('Save')"/>
</form>
