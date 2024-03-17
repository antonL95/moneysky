<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <x-form-section submit="update({{$ticker}})">
            <x-slot name="title">Stock ticker</x-slot>
            <x-slot name="description">Update ticker and/or it's amount</x-slot>

            <x-slot name="form">
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-input label="{{__('Ticker')}}" wire:model="form.ticker"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-ts-number label="{{__('Amount')}}" wire:model="form.amount"/>
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
