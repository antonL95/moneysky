@if(!auth()->user()->subscribed())
<span class="inline-flex bg-yellow-100 text-dark-900 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-primary-50">
    {{ __('Pro') }}
</span>
@endif
