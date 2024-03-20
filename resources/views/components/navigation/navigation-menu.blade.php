<nav class="px-4 py-2.5 fixed left-0 right-0 top-0 z-20">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            @auth
                <label for="main-drawer" class="lg:hidden mr-5">
                    <svg aria-hidden="true"
                         class="w-6 h-6"
                         fill="currentColor"
                         viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    <svg aria-hidden="true"
                         class="hidden w-6 h-6"
                         fill="currentColor"
                         viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                    <span class="sr-only">Toggle sidebar</span>
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
            <!-- Dark Mode -->
            {{--            <livewire:dark-mode-switcher />--}}
            <x-mary-theme-toggle class="ml-4"/>
        </div>
    </div>
</nav>
