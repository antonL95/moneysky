<li>
    <a href="{{$route}}"
       wire:navigate
       class="{{ request()->fullUrlIs($route) ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50'}} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
        @isset($icon)
            {{$icon}}
        @endif
        {{ $title ?? 'Item' }}
    </a>
</li>
