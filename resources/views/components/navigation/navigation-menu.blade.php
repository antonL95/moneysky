<nav class="px-4 py-2.5 fixed left-0 right-0 top-0 z-20">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            @auth
                <label for="main-drawer" class="lg:hidden mr-5">
                    <x-mary-icon name="fas.bars-staggered" class="w-6 h-6 text-gray-600 dark:text-gray-400"/>
                </label>
            @endauth
            <a href="{{Auth::user() ? route('app.home') : route('home')}}"
               wire:navigate
               class="flex items-center justify-between mr-4">
                <x-application-logo
                    class="w-10 h-10 mr-2 fill-dark-900 stroke-dark-900 dark:stroke-white dark:fill-white"/>
                <span
                    class="hidden sm:inline-flex self-center text-2xl font-semibold whitespace-nowrap dark:text-white">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>
        <div class="flex items-center lg:order-2">
            @auth

                <x-mary-dropdown>
                    <x-slot:trigger>
                        <button type="button"
                                class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                        >
                            <span class="sr-only">Open user menu</span>
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="w-8 h-8 rounded-full"
                                     src="{{ Auth::user()->profile_photo_url }}"
                                     alt="{{ Auth::user()->name }}">
                            @else
                                <span class="inline-flex rounded-md">
                            <x-mary-avatar/>
                        </span>
                            @endif
                        </button>
                    </x-slot:trigger>

                    <x-mary-menu-item title="{{__('My profile')}}" link="{{route('profile.show')}}"/>
                    <x-mary-menu-item title="{{__('Billing')}}" link="{{route('billing')}}" no-wire-navigate />

                    <x-mary-menu-separator/>

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-mary-menu-item title="{{ __('Sign out') }}"
                                          link="{{ route('logout') }}"
                                          @click.prevent="$root.submit();">
                        </x-mary-menu-item>
                    </form>

                </x-mary-dropdown>
            @else
                <x-mary-button class="btn-primary" link="{{route('login')}}" wire:navigate
                               class="mx-2">
                    {{__('Log in')}}
                </x-mary-button>
                <x-mary-button class="btn-primary" link="{{route('register')}}"
                               class="btn-primary">
                    {{__('Start')}}
                </x-mary-button>
            @endauth

            <x-mary-theme-toggle class="ml-4"/>
        </div>
    </div>
</nav>
