@props(['transactions' => [], 'userCurrency' => ''])

<div class="overflow-x-auto">

    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead
            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">
                {{__('Bank account')}}
            </th>
            <th scope="col" class="px-4 py-3">
                {{__('Transaction amount')}}
            </th>
            <th scope="col" class="px-4 py-3">
                {{__('Currency')}}
            </th>
            <th scope="col" class="px-4 py-3">
                {{__('Category')}}
            </th>
            <th scope="col" class="px-4 py-3">
                {{__('Information')}}
            </th>
            <th scope="col" class="px-4 py-3">
                {{__('Booked at')}}
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                <th scope="row"
                    class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                    {{ $transaction->userBankAccount->name }}
                </th>
                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                    <div class="flex items-center">
                        <x-amount-format :amount="$transaction->balance_cents"
                                         :amount-currency="$transaction->currency"/>
                    </div>
                </td>
                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                    <span
                        class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                        {{ $transaction->currency }}
                    </span>
                </td>
                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                    <span
                        class="text-white text-xs font-medium px-2 py-0.5 rounded"
                        style="background-color: {{$transaction->userTransactionTag->color ?? $transaction->transactionTag->color}}">
                        {{ $transaction->userTransactionTag->tag ?? $transaction->transactionTag->tag ?? __('Unknown') }}
                    </span>
                </td>
                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                    {{ $transaction->description }}
                </td>
                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-primary-50">
                    <span
                        class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                        {{ $transaction->booked_at?->diffForHumans() }}
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $transactions->links('components.table-pagination') }}
</div>
