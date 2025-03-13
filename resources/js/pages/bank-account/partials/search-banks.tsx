import { useEffect, useState } from 'react';
import axios from 'axios';
import {
    Combobox,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    Dialog,
    DialogPanel,
    Transition,
    TransitionChild,
} from '@headlessui/react';
import { clsx } from 'clsx';
import { router } from '@inertiajs/react';
import BankInstitutionData = App.Data.App.BankAccount.BankInstitutionData;
import { Search, StopCircle } from 'lucide-react';

export default function SearchBanks({ open, setOpen, banks }: { open: boolean; setOpen: (open: boolean) => void; banks?: BankInstitutionData[]}) {
    const [query, setQuery] = useState('');

    useEffect(() => {
        const timeoutId = setTimeout(() => {
            router.reload({
                data: {q: query},
                only: ['banks'],
            })
        }, 250);

        return () => clearTimeout(timeoutId);
    }, [query]);

    const connectBank = (item: BankInstitutionData) => {
        router.get(route('bank-account.redirect', { id: item.id }), {});
    };

    return (
        <Transition show={open} afterLeave={() => setQuery('')} appear>
            <Dialog className="relative z-10" onClose={setOpen}>
                <div className="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">
                    <TransitionChild
                        enter="ease-out duration-300"
                        enterFrom="opacity-0 scale-95"
                        enterTo="opacity-100 scale-100"
                        leave="ease-in duration-200"
                        leaveFrom="opacity-100 scale-100"
                        leaveTo="opacity-0 scale-95"
                    >
                        <DialogPanel className="mx-auto max-w-xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
                            <Combobox>
                                <div className="relative">
                                    <Search
                                        className="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400"
                                        aria-hidden="true"
                                    />
                                    <ComboboxInput
                                        autoFocus
                                        className="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                                        placeholder="Search..."
                                        onChange={(e) => {
                                            setQuery(e.target.value);
                                        }}
                                    />
                                </div>

                                {banks && banks.length > 0 && (
                                    <ComboboxOptions
                                        static
                                        className="max-h-96 transform-gpu scroll-py-3 overflow-y-auto p-3"
                                    >
                                        {banks.map((item) => (
                                            <ComboboxOption
                                                key={item.id}
                                                value={item}
                                                className={({ focus }) =>
                                                    clsx(
                                                        'flex cursor-default select-none rounded-xl p-3',
                                                        focus && 'bg-gray-100',
                                                    )
                                                }
                                                onClick={() => connectBank(item)}
                                            >
                                                {({ focus }) => (
                                                    <>
                                                        <div
                                                            className={clsx(
                                                                'flex h-10 w-10 flex-none items-center justify-center rounded-lg',
                                                            )}
                                                        >
                                                            <img
                                                                src={item.logo}
                                                                alt={item.name}
                                                                className={`rounded-full`}
                                                            />
                                                        </div>
                                                        <div className="ml-4 flex-auto">
                                                            <p
                                                                className={clsx(
                                                                    'text-sm font-medium',
                                                                    focus ? 'text-gray-900' : 'text-gray-700',
                                                                )}
                                                            >
                                                                {item.name}
                                                            </p>
                                                            <p
                                                                className={clsx(
                                                                    'text-sm',
                                                                    focus ? 'text-gray-700' : 'text-gray-500',
                                                                )}
                                                            >
                                                                {item.countries}
                                                            </p>
                                                        </div>
                                                    </>
                                                )}
                                            </ComboboxOption>
                                        ))}
                                        {query.length === 0 && (
                                            <p className="mt-4 text-center font-semibold text-gray-900">
                                                Search for more...
                                            </p>
                                        )}
                                    </ComboboxOptions>
                                )}

                                {query !== '' && (!banks || banks.length === 0) && (
                                    <div className="px-6 py-14 text-center text-sm sm:px-14">
                                        <StopCircle className="mx-auto h-6 w-6 text-gray-400" />
                                        <p className="mt-4 font-semibold text-gray-900">No results found</p>
                                        <p className="mt-2 text-gray-500">
                                            No supported banks found for this search term. Please try again.
                                        </p>
                                    </div>
                                )}
                            </Combobox>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </Transition>
    );
}
