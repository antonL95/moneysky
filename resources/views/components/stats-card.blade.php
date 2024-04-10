<div class="relative overflow-hidden rounded-lg bg-white px-4 pt-4 shadow max-h-20">
    <dt>
        @isset($icon)
            <div class="absolute rounded-md bg-indigo-500 p-2">
                <x-ts-icon class="h-6 w-6 text-white" :name="$icon"/>
            </div>
        @endisset
        @isset($title)
            <p class="ml-14 truncate text-sm font-medium text-gray-500">
                {{ $title }}
            </p>
        @endisset
    </dt>
    <dd class="ml-14 flex items-baseline sm:pb-7">
        <p class="text-md font-semibold text-gray-900">
            {{ $value }}
        </p>
        @isset($trendValue)
            <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                @if($trendValue > 0)
                    <x-ts-icon name="trend-up" class="h-5 w-5 flex-shrink-0 self-center text-green-500"/>
                @else
                    <x-ts-icon name="trend-down" class="h-5 w-5 flex-shrink-0 self-center text-red-500"/>
                @endif
                {{ $trendValue }}
            </p>
        @endisset
    </dd>
</div>
