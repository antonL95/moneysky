<div
    x-data="{
        openSidebar: false,
        toggleSidebar() {
            this.openSidebar = ! this.openSidebar
        },
    }"
    x-on:keydown.escape="openSidebar = false"
>
    <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak>
        <div
            x-show="openSidebar"
            x-transition:enter="transition-opacity duration-300 ease-linear"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-300 ease-linear"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/80"
            x-cloak
        ></div>

        <div x-show="openSidebar" class="fixed inset-0 flex">
            <div
                x-show="openSidebar"
                x-transition:enter="transform transition duration-300 ease-in-out"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition duration-300 ease-in-out"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="relative mr-16 flex w-full max-w-xs flex-1"
                x-on:click.outside="toggleSidebar()"
            >
                <div
                    x-show="openSidebar"
                    x-transition:enter="duration-300 ease-in-out"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="duration-300 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute left-full top-0 flex w-16 justify-center pt-5"
                >
                    <button x-on:click="toggleSidebar()" type="button" class="-m-2.5 p-2.5">
                        <span class="sr-only">Close sidebar</span>
                        <x-ts-icon name="x" class="h-6 w-6 text-white" />
                    </button>
                </div>

                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                    <div class="mb-5 flex shrink-0 items-center">
                        <a href="{{ route('home') }}" wire:navigate class="inline-flex pt-10">
                            <x-application-logo class="h-8 w-8" />
                            <span class="pl-2 text-xl font-bold">
                                {{ config('app.name') }}
                            </span>
                        </a>
                    </div>
                    <x-navigation.nav-sidebar-list />
                </div>
            </div>
        </div>
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
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" x-on:click="toggleSidebar()">
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
