<nav class="px-4 py-2.5 fixed left-0 right-0 top-0 z-20">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            @auth
                <button data-drawer-target="drawer-navigation"
                    data-drawer-toggle="drawer-navigation"
                    aria-controls="drawer-navigation"
                    class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-primary-50">
                    <svg aria-hidden="true"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
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
                </button>
            @endauth
            <a href="{{Auth::user() ? route('app.home') : route('home')}}"
               wire:navigate
               class="flex items-center justify-between mr-4">
                <x-application-logo class="w-10 h-10 mr-2 fill-dark-900 stroke-dark-900 dark:stroke-white dark:fill-white" />
                <span class="hidden sm:inline-flex self-center text-2xl font-semibold whitespace-nowrap dark:text-primary-50">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>
        <div class="flex items-center lg:order-2">
            @auth
                <button type="button"
                    class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                    id="user-menu-button"
                    aria-expanded="false"
                    data-dropdown-toggle="dropdown">
                    <span class="sr-only">Open user menu</span>
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-8 h-8 rounded-full"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}">
                    @else
                        <span class="inline-flex rounded-md">
                            <x-ts-avatar sm />
                        </span>
                    @endif
                </button>

                <!-- Dropdown menu -->
                <div
                    class="hidden z-50 my-4 w-56 text-base list-none bg-primary-50 rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl overflow-hidden"
                    id="dropdown">
                    <div class="py-3 px-4">
                        <span class="block text-sm font-semibold text-gray-900 dark:text-primary-50">
                          {{ Auth::user()->name }}
                        </span>
                        <span class="block text-sm text-gray-900 truncate dark:text-primary-50">
                            {{ Auth::user()->email }}
                        </span>
                    </div>
                    <ul class="py-1 text-gray-700 dark:text-gray-300"
                        aria-labelledby="dropdown">
                        <li>
                            <a href="{{route('profile.show')}}" wire:navigate
                               class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-primary-50">
                                {{__('My profile')}}
                            </a>
                        </li>
                        <li>
                            <a href="@if(auth()->user()->subscribed()) {{route('billing')}} @else {{route('subscription-checkout', ['plan' => 'monthly'])}} @endif"
                               class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-primary-50">
                                {{__('Billing')}}
                            </a>
                        </li>
                    </ul>
                    <ul class="pt-1 text-gray-700 dark:text-gray-300"
                        aria-labelledby="dropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <a href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();"
                                   class="block pt-2 pb-3 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-primary-50">
                                    {{ __('Sign out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{route('login')}}" wire:navigate
                   class="text-dark-900 dark:text-primary-50 hover:bg-primary-50 focus:ring-4 focus:ring-dark-900 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                    {{__('Log in')}}
                </a>
                <a href="{{route('register')}}"
                   class="text-primary-50 bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                    {{__('Start')}}
                </a>
            @endauth
            <!-- Dark Mode -->
            <livewire:dark-mode-switcher key="{{Str::random(32)}}"/>
        </div>
    </div>
</nav>
