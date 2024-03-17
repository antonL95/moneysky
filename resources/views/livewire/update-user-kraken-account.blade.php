<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="update({{$account}})">
            <x-slot name="title">Kraken account</x-slot>
            <x-slot name="description">Update your kraken account</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-input label="{{ __('Api key') }}" wire:model="form.apiKey"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-password label="{{ __('Private key') }}" autocomplete="off" wire:model="form.privateKey"/>
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
