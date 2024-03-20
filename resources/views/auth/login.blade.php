<x-app-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        <x-validation-errors class="mb-4"/>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif
        <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            {{__('Sign in to your account')}}
        </h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-6">
            @csrf
            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="email" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required />
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <x-checkbox name="remember" id="remember" aria-describedby="remember"/>
                    </div>
                    <div class="ml-3 text-sm">
                        <x-label for="remember" value="{{__('Remember me')}}"></x-label>
                    </div>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-medium text-primary-600 hover:underline dark:text-white0">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <x-mary-button class="w-full btn-primary" type="submit">
                {{ __('Log in') }}
            </x-mary-button>
            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                {{__('Don’t have an account yet?')}}
                <a href="{{ route('register') }}"
                   class="font-medium text-primary-600 hover:underline dark:text-white0">{{__('Sign up')}}</a>
            </p>
        </form>
    </x-authentication-card>
</x-app-layout>
