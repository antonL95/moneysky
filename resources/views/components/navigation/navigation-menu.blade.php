<div>
    <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <x-ts-slide id="side-bar-navigation">
            <x-slot:title>
                <div class="mb-5 flex shrink-0 items-center">
                    <a href="{{ route('home') }}" wire:navigate class="inline-flex">
                        <x-application-logo class="h-8 w-8" />
                        <span class="pl-2 text-xl font-bold">
                            {{ config('app.name') }}
                        </span>
                    </a>
                </div>
                <x-navigation.nav-sidebar-list />
            </x-slot>
        </x-ts-slide>
    </div>

    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
            <div class="flex h-16 shrink-0 items-center">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex">
                    <x-application-logo class="h-8 w-8" />
                    <span class="pl-2 text-xl font-bold">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>
            <x-navigation.nav-sidebar-list />
        </div>
    </div>

    <div class="lg:pl-72">
        <div
            class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8"
        >
            <button
                type="button"
                class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                x-on:click="$slideOpen('side-bar-navigation')"
            >
                <span class="sr-only">{{ __('Open sidebar') }}</span>
                <x-ts-icon name="list" class="h-6 w-6" />
            </button>
            <x-navigation.profile-avatar-dropdown />
        </div>

        <main class="py-10">
            <div class="max-w-10/12 mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
