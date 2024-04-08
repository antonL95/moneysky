<form class="max-w-sm mx-auto" wire:submit="connect">
    <x-ts-select.styled
        label="{{ __('Chose your bank') }}"
        :request="route('app.list-institutions')"
        select="label:name|value:id"
        wire:model="institution"
        searchable/>
    <x-button type="submit" class="mt-2">
        <x-slot:title>
            {{ __('Connect') }}
        </x-slot:title>
    </x-button>
</form>
