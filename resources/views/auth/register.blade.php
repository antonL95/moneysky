<x-layouts.guest>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        <h1 class="text-xl font-bold leading-tight tracking-tight  md:text-2xl ">
            {{__('Sign up')}}
        </h1>


        <form method="POST" action="{{ route('register') }}" class="space-y-4 md:space-y-6">
            @csrf

            <x-mary-input placeholder="{{__('Name')}}" id="name" type="text" name="name" :value="old('name')" required
                          autocomplete="name"
                          label="{{__('Name')}}"/>
            <x-mary-input placeholder="{{__('Email')}}" id="email" type="email" name="email" :value="old('email')"
                          required autocomplete="email" label="{{__('Email')}}"/>
            <x-mary-input placeholder="{{__('Password')}}" id="password" type="password" name="password" required
                          label="{{__('Password')}}" autocomplete="new-password"/>
            <x-mary-input placeholder="{{__('Confirm Password')}}" id="password_confirmation" type="password"
                          name="password_confirmation" required label="{{__('Confirm Password')}}"
                          autocomplete="new-password"/>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <div class="flex items-center">
                        <x-mary-checkbox name="terms" id="terms" required>
                            <x-slot:label>
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm  dark:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm  dark:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </x-slot:label>
                        </x-mary-checkbox>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm  dark:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                   href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-mary-button class="ms-4 btn btn-primary" type="submit">
                    {{ __('Register') }}
                </x-mary-button>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>
