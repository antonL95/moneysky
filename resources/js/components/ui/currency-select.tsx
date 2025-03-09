import { useState } from 'react';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { Check, ChevronsUpDown } from 'lucide-react';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { cn } from '@/lib/utils';

export default function({
    currencies,
    setCurrency,
    selected,
    modal = true
}: {
    currencies: { [key: string]: string };
    setCurrency: (c: string) => void;
    selected?: string;
    modal?: boolean;
}) {
    const [selectedCurrency, setSelectedCurrency] = useState(selected);
    const [open, setOpen] = useState(false);

    return (
        <Popover open={open} onOpenChange={setOpen} modal={modal}>
            <PopoverTrigger asChild>
                <Button variant="outline" role="combobox" aria-expanded={open} className="w-full justify-between">
                    {selectedCurrency ? selectedCurrency : 'Select currency...'}
                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="h-[200px] w-[160px] p-0">
                <Command>
                    <CommandInput placeholder={'Search...'} />
                    <CommandList>
                        <CommandEmpty>No currency found.</CommandEmpty>
                        <CommandGroup>
                            {Object.keys(currencies).map((key: string) => (
                                <CommandItem
                                    key={key}
                                    value={currencies[key]}
                                    onSelect={(currentValue: string) => {
                                        setSelectedCurrency(currentValue);
                                        setCurrency(currentValue);
                                        setOpen(false);
                                    }}
                                >
                                    <Check
                                        className={cn(
                                            'mr-2 h-4 w-4',
                                            selectedCurrency === currencies[key] ? 'opacity-100' : 'opacity-0'
                                        )}
                                    />
                                    {currencies[key]}
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
