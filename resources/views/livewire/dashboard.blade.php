<div class="antialiased">
    <main class="p-4 md:ml-64 h-auto pt-20">
        <div class="flex justify-center sm:h-96">
            <div class="w-full p-4 md:p-6">
                <livewire:livewire-pie-chart
                    class="bg-white dark:bg-grey-800"
                    :pie-chart-model="$pieChartModel"/>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
            <x-transactions-table :transactions="$transactions" :user-currency="$userCurrency"/>
        </div>
    </main>
</div>
