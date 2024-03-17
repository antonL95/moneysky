<button {{ $attributes->merge(['type' => 'submit', 'class' => 'text-primary-50 bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-primary-50  dark:focus:ring-primary-900']) }}>
    {{ $slot }}
</button>
