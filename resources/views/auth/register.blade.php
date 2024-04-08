<x-guest-layout>
    <x-authentication-card>
        <x-slot:headline>
            {{__('Sign up')}}
        </x-slot:headline>

        <form method="POST" action="{{ route('register') }}" class="space-y-4 md:space-y-6">
            @csrf

            <x-ts-input placeholder="{{__('Name')}}" id="name" type="text" name="name" :value="old('name')" required
                          autocomplete="name"
                          label="{{__('Name')}}"/>
            <x-ts-input placeholder="{{__('Email')}}" id="email" type="email" name="email" :value="old('email')"
                          required autocomplete="email" label="{{__('Email')}}"/>
            <x-ts-input placeholder="{{__('Password')}}" id="password" type="password" name="password" required
                          label="{{__('Password')}}" autocomplete="new-password"/>
            <x-ts-input placeholder="{{__('Confirm Password')}}" id="password_confirmation" type="password"
                          name="password_confirmation" required label="{{__('Confirm Password')}}"
                          autocomplete="new-password"/>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <div class="flex items-center">
                        <x-ts-checkbox name="terms" id="terms" required>
                            <x-slot:label>
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm  dark:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm  dark:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </x-slot:label>
                        </x-ts-checkbox>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <x-button class="w-full" type="submit" title="{{ __('Register') }}"/>
            </div>
            <a class="underline text-sm"
               href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </form>
    </x-authentication-card>
</x-guest-layout>
