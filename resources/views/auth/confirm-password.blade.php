<x-layouts.guest>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        <div class="mb-4 text-sm  ">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-mary-input id="password" class="block mt-1 w-full" type="password" name="password" required
                              autocomplete="current-password" autofocus label="{{ __('Password') }}"/>
            </div>

            <div class="flex justify-end mt-4">
                <x-mary-button type="submit" class="btn btn-primary ms-4">
                    {{ __('Confirm') }}
                </x-mary-button>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>
