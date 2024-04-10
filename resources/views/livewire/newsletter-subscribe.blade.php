<div class="py-16 sm:py-24 lg:py-24">
    <div class="mx-auto max-w-7xl flex flex-col lg:flex-row gap-y-2 justify-between">
        <div class="max-w-5/12 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:col-span-7">
            <h2 class="inline sm:block lg:inline xl:block">Want product news and updates?</h2>
            <p class="inline sm:block lg:inline xl:block">Sign up for our newsletter.</p>
        </div>
        <form wire:submit="subscribe" class="w-full lg:w-5/12 lg:col-span-5 lg:pt-2">
            <div class="flex gap-x-4">
                <input type="email" wire:model="email"
                            required
                            class="min-w-0 w-full flex-auto rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Enter your email"/>
                @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <button type="submit" class="flex-none rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Subscribe</button>
            </div>
        </form>
    </div>
</div>
