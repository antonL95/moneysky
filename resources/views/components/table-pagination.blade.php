<div>
    <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
         role="navigation" aria-label="Table navigation">
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    {{ __('Total') }}: <span
                    class="font-semibold text-gray-900 dark:text-primary-50">{{$paginator->total()}}</span>
            </span>
        <ul class="inline-flex items-stretch -space-x-px">
            @if($paginator->hasPages())
                <li>
                    @if ($paginator->onFirstPage())
                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" disabled
                                class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-primary-50 rounded-l-lg border border-gray-300">
                            <span class="sr-only">Previous</span>
                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    @else
                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                                class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-primary-50 rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-primary-50">
                            <span class="sr-only">Previous</span>
                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    @endif
                </li>
                <li>
                    @if ($paginator->onLastPage())
                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" disabled
                                class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-primary-50 rounded-r-lg border border-gray-300">
                            <span class="sr-only">Next</span>
                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    @else
                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                                class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-primary-50 rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-primary-50">
                            <span class="sr-only">Next</span>
                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    @endif
                </li>
            @endif
        </ul>
    </nav>
</div>
