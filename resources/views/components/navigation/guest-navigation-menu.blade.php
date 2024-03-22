<nav class="px-4 py-2.5 fixed left-0 right-0 top-0 z-20">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            <a href="{{Auth::user() ? route('app.home') : route('home')}}"
               wire:navigate
               class="flex items-center justify-between mr-4">
                <x-application-logo class="w-10 h-10 mr-2 fill-dark-900 stroke-dark-900 dark:stroke-white dark:fill-white"/>
                <span class="hidden sm:inline-flex self-center text-2xl font-semibold whitespace-nowrap">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>
        <div class="flex items-center lg:order-2">
                <x-mary-button class="btn btn-primary" link="{{route('login')}}" wire:navigate
                               class="mx-2">
                    {{__('Log in')}}
                </x-mary-button>
                <x-mary-button class="btn btn-primary" link="{{route('register')}}"
                               class="btn btn-primary">
                    {{__('Start')}}
                </x-mary-button>
            <x-mary-theme-toggle class="ml-4"/>
        </div>
    </div>
</nav>
