<x-layouts.guest>
    <x-authentication-card>
        <x-slot:headline>
            {{__('Forgot password')}}
        </x-slot:headline>

        <div class="mb-4 text-sm  ">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-ts-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus label="{{ __('Email') }}" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button type="submit" class="btn btn-primary" :title="__('Email Password Reset Link')"/>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>
