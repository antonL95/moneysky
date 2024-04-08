<x-guest-layout>
    <x-authentication-card>
        <x-slot:headline>
            {{ __('Verify email') }}
        </x-slot:headline>
        {{ __('Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}

        @if (session('status') === 'verification-link-sent')
            <div class="mb-4 font-medium text-sm">
                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit">
                        <x-slot:title>
                            {{ __('Resend Verification Email') }}
                        </x-slot:title>
                    </x-button>
                </div>
            </form>

            <div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <x-button type="submit" class="bg-red-800 hover:bg-red-900">
                        <x-slot:title>
                            {{ __('Log Out') }}
                        </x-slot:title>
                    </x-button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
