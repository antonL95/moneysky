<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="create">
            <x-slot name="title">Stock ticker</x-slot>
            <x-slot name="description">Create ticker with amount</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-input label="{{__('Ticker')}}" id="ticker" type="text" wire:model="form.ticker"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-number label="{{__('Amount')}}" id="amount" wire:model="form.amount"/>
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
