<div class="antialiased">
    <main class="p-4 md:ml-64 pt-20 min-h-dvh">
        <div class="flex justify-center h-dvh sm:h-96">
            <div class="w-full p-4 md:p-6">
                <livewire:livewire-pie-chart
                    key="{{Str::random(32)}}"
                    class="bg-primary-50 dark:bg-grey-800"
                    :pie-chart-model="$pieChartModel"/>
            </div>
        </div>
        <div class="relative overflow-hidden bg-primary-50 shadow-md dark:bg-gray-800 sm:rounded-lg">
            <x-transactions-table :transactions="$transactions"/>
        </div>
    </main>
</div>
