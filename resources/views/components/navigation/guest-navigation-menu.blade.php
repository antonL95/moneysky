<nav class="px-4 py-2.5 fixed left-0 right-0 top-0 z-20">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            <a href="{{route('home')}}"
               wire:navigate
               class="flex items-center justify-between mr-4">
                <x-application-logo
                    class="w-10 h-10 mr-2 fill-dark-900 stroke-dark-900 dark:stroke-white dark:fill-white"/>
                <span class="inline-flex self-center text-2xl font-semibold whitespace-nowrap ">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>
        @auth
            <a href="{{route('app.home')}}"
                       wire:navigate
                       class="text-sm font-semibold leading-6 text-gray-900">
                {{ __('Dashboard') }}&nbsp;<span aria-hidden="true">&rarr;</span>
            </a>
        @else
            <a href="{{route('login')}}"
                       wire:navigate
                       class="text-sm font-semibold leading-6 text-gray-900">
                {{ __('Log in') }}&nbsp;<span aria-hidden="true">&rarr;</span>
            </a>
        @endauth
    </div>
</nav>
