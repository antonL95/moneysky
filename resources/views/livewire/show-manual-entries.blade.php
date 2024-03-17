<div>
    <main class="p-4 md:ml-64 h-auto pt-20">
        <section class="py-3 sm:py-5">
            <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
                <div class="relative overflow-hidden bg-primary-50 shadow-md dark:bg-gray-800 sm:rounded-lg">
                    <div
                        class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                        <div class="flex items-center flex-1 space-x-4">
                        </div>
                        <div
                            class="flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center lg:justify-end md:space-y-0 md:space-x-3">
                            <x-button-link
                                href="{{route('app.add-manual-entry')}}"
                                class="inline-flex"
                                type="button">
                                <x-fas-plus class="w-[20px] h-[20px] pr-2"/>
                                {{ __('Add cash wallet') }}
                            </x-button-link>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">
                                    {{__('Name')}}
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    {{__('Amount')}}
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    {{__('Currency')}}
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">{{ __('Action') }}</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($wallets as $wallet)
                                <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                                        <div class="flex items-center">
                                            <span
                                                class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                                {{ $wallet->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                                        <div class="flex items-center">
                                            <x-amount-format :amount="$wallet->amount_cents"
                                                             :amount-currency="$wallet->currency"/>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                                        <div class="flex items-center">
                                            <span
                                                class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                                {{ $wallet->currency }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 flex items-center justify-end">
                                        <button id="{{$wallet->id}}-dropdown-button"
                                                data-dropdown-toggle="{{$wallet->id}}-dropdown"
                                                class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                                                type="button">
                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                 viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                        </button>
                                        <div id="{{$wallet->id}}-dropdown"
                                             class="hidden z-1000 w-44 bg-primary-50 rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                                aria-labelledby="{{$wallet->id}}-dropdown-button">
                                                <li>
                                                    <a href="{{route('app.update-manual-entry', ['wallet' => $wallet->id])}}"
                                                       wire:navigate
                                                       class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-primary-50">Edit</a>
                                                </li>
                                            </ul>
                                            <div class="py-1">
                                                <a
                                                    type="button"
                                                    wire:click="delete({{$wallet->id}})"
                                                    wire:confirm="Are you sure you want to delete this post?"
                                                    class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-primary-50 cursor-pointer">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $wallets->links('components.table-pagination') }}
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
