<div class="flex flex-col justify-center self-center justify-self-center py-12 sm:px-6 lg:px-8 items-center h-full">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <x-application-logo class="mx-auto h-10 w-auto flex justify-center stroke-dark-900 fill-dark-900" />

        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            {{ $headline }}
        </h2>

    </div>
    <div class="mt-10 w-11/12 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-gray-50 px-6 py-12 shadow sm:rounded-lg sm:px-12">
            {{ $slot }}
        </div>
    </div>
</div>
