<section>
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-md sm:text-center">
            <h2 class="mb-4 text-3xl tracking-tight font-extrabold  sm:text-4xl ">Sign up
                for our newsletter</h2>
            <p class="mx-auto mb-8 max-w-2xl font-light  md:mb-12 sm:text-xl ">Stay up to
                date with the roadmap progress, announcements and exclusive discounts feel free to sign up with your
                email.</p>
            <x-mary-form wire:submit="subscribe">
                <x-mary-input placeholder="{{__('Email')}}" wire:model="email">
                    <x-slot:append>
                        <x-mary-button label="{{__('Subscribe')}}" type="submit" icon="o-check"
                                  class="btn btn-primary rounded-l-none"/>
                    </x-slot:append>
                </x-mary-input>
                <div
                    class="mx-auto max-w-screen-sm text-sm text-left  newsletter-form-footer ">
                    We care about the protection of your data. <a href="{{route('policy.show')}}"
                                                                  class="font-medium -600 0 hover:underline">Read
                        our Privacy Policy</a>.
                </div>
            </x-mary-form>
        </div>
    </div>
</section>
