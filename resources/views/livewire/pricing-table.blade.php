<div class="rounded-2xl py-10 text-center ring-1 ring-inset ring-gray-900/5 lg:flex lg:flex-col lg:justify-center lg:py-16">
    <div class="mx-auto max-w-xs px-8">
        <p class="font-semibold">
            {{ __('Per month') }}
        </p>
        <p class="mt-6 flex items-baseline justify-center gap-x-2">
            <span class="text-5xl font-bold tracking-tight">
                {{ $amount }}
            </span>
        </p>
        <x-mary-select wire:model.live="currency" :options="$currencies"/>
        <x-mary-button class="btn btn-primary" link="{{route('register')}}" class="mt-10 btn btn-primary w-full" no-wire-navigate>
            {{ __('Get Started') }}
        </x-mary-button>
    </div>
</div>
