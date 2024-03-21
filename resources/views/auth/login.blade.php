<x-layouts.guest>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

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
            <x-mary-input id="email" type="email" name="email" :value="old('email')" required autocomplete="email"
                          placeholder="{{__('Email')}}"/>
            <x-mary-input id="password" type="password" name="password" required placeholder="{{__('Password')}}"/>

            <div class="flex items-center justify-between">
                <div class="flex items-start">
                    <x-mary-checkbox name="remember" label="{{ __('Remember me') }}" id="remember" :checked="old('remember')"/>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-medium text-primary-600 hover:underline dark:text-white0">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <x-mary-button class="w-full btn btn-primary" type="submit">
                {{ __('Log in') }}
            </x-mary-button>
            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                {{__('Don’t have an account yet?')}}
                <a href="{{ route('register') }}"
                   class="font-medium text-primary-600 hover:underline dark:text-white0">{{__('Sign up')}}</a>
            </p>
        </form>
    </x-authentication-card>
</x-layouts.guest>
