<form wire:submit="create">
    <x-ts-input label="{{__('Ticker')}}" wire:model="form.ticker" />
    <x-ts-number label="{{__('Amount')}}" wire:model="form.amount" step="0.0001"/>

    <x-button type="submit" class="btn btn-primary" :title="__('Save')" />
</form>
