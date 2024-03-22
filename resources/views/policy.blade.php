<x-layouts.guest>
    <div class="pt-20">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-authentication-card-logo />
            </div>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-primary-50 dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert">
                {!! $policy !!}
            </div>
        </div>
    </div>
</x-layouts.guest>
