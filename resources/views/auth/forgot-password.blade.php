<x-layouts.guest>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm  ">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm  ">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-mary-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus label="{{ __('Email') }}" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-mary-button type="submit" class="btn btn-primary">
                    {{ __('Email Password Reset Link') }}
                </x-mary-button>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>
