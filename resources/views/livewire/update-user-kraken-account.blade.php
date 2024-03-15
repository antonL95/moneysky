<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="update({{$account->id}})">
            <x-slot name="title">Kraken account</x-slot>
            <x-slot name="description">Manage your kraken account</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="apiKey" value="{{ __('Api key') }}"/>
                    <x-input id="apiKey" type="text" wire:model="form.apiKey"/>
                    <x-input-error for="form.apiKey"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="apiKey" value="{{ __('Private key') }}"/>
                    <x-input id="apiKey" type="password" autocomplete="off" wire:model="form.privateKey"/>
                    <x-input-error for="form.privateKey"/>
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
