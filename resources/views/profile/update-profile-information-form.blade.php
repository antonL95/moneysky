<x-form-section submit="updateProfileInformation">
    <x-slot:title>
        {{ __('Profile Information') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot:description>

    <x-slot:form>
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <x-ts-upload label="Profile photo" wire:model="photo" accept="image/png, image/jpeg">
                <img src="{{ $this->user->profile_photo_url ?? '/empty-user.jpg' }}" alt="{{ $this->user->name }}" class="h-40 rounded-full" />
            </x-ts-upload>
        @endif
        <div>
            <x-ts-input label="{{ __('Name') }}" wire:model="state.name" required/>
            <x-ts-input label="{{ __('Email') }}" wire:model="state.email" type="email" required/>
        </div>
    </x-slot:form>

    <x-slot:actions>
        <div class="mt-2">
            <x-button wire:loading.attr="disabled" wire:target="photo" :title="__('Save')" type="submit" />
        </div>
    </x-slot:actions>
</x-form-section>
