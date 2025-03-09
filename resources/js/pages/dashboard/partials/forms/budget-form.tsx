import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import CurrencySelect from '@/components/ui/currency-select';
import { FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { useForm } from '@inertiajs/react';
import { Check, ChevronsUpDown } from 'lucide-react';
import { FormEventHandler, useState } from 'react';
import UserBudgetData = App.Data.App.Dashboard.UserBudgetData;
import BudgetData = App.Data.App.Dashboard.BudgetData;

export default function ({
    budget,
    tags,
    closeModal,
    currencies,
}: {
    budget?: UserBudgetData;
    tags: { id: number; name: string }[];
    closeModal: () => void;
    currencies: { [key: string]: string };
}) {
    const { data, setData, errors, post, put, processing, reset } = useForm<BudgetData>({
        name: budget?.name || '',
        balance: budget?.budget || 0,
        currency: budget?.currency || '',
        tags: budget?.tags || [],
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (budget !== undefined) {
            put(
                route('budget.update', {
                    budget: budget.budgetId,
                }),
                {
                    onSuccess: () => {
                        setTimeout(() => {
                            reset();
                            closeModal();
                        }, 100);
                    },
                    only: ['budgets', 'flash'],
                },
            );
        } else {
            post(route('budget.store'), {
                onSuccess: () => {
                    setTimeout(() => {
                        reset();
                        closeModal();
                    }, 100);
                },
                only: ['budgets', 'flash'],
            });
        }
    };

    return (
        <form onSubmit={submit} className="mt-6 space-y-6">
            <div className="grid gap-2">
                <Label htmlFor={'name'}>Name</Label>
                <Input
                    id={'name'}
                    name={'name'}
                    value={data.name}
                    onChange={(e) => {
                        setData('name', e.target.value);
                    }}
                    autoFocus={true}
                />
                {errors.name !== undefined && <FormMessage>{errors.name}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <Label htmlFor={'balance'}>Amount</Label>
                <Input
                    id={'balance'}
                    name={'balance'}
                    value={data.balance}
                    type={'number'}
                    step={'0.001'}
                    onChange={(e) => {
                        setData('balance', Number.parseFloat(e.target.value));
                    }}
                />
                {errors.balance !== undefined && <FormMessage>{errors.balance}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <TagsSelect
                    tags={tags}
                    selected={data.tags}
                    setSelected={(tags) => {
                        setData('tags', tags);
                    }}
                />
                {errors.tags !== undefined && <FormMessage>{errors.tags}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <CurrencySelect
                    selected={budget?.currency || ''}
                    currencies={currencies}
                    setCurrency={(c) => {
                        setData('currency', c);
                    }}
                />
                {errors.currency !== undefined && <FormMessage>{errors.currency}</FormMessage>}
            </div>

            <div className="flex items-center gap-4">
                <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                    {budget !== undefined ? 'Update' : 'Create'}
                </Button>
            </div>
        </form>
    );
}

function TagsSelect({
    tags,
    selected,
    setSelected,
}: {
    tags: { id: number; name: string }[];
    selected: number[] | null;
    setSelected: (tags: number[]) => void;
}) {
    const [open, setOpen] = useState(false);
    const [selectedTags, setSelectedTags] = useState<number[]>(selected || []);

    return (
        <Popover open={open} onOpenChange={setOpen} modal={true}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className="w-full max-w-full justify-between overflow-hidden"
                >
                    {selectedTags.length > 0
                        ? selectedTags
                              ?.map((tagId) => {
                                  return tags.filter((tag) => {
                                      return tag.id === tagId;
                                  })[0].name;
                              })
                              .join(', ')
                        : 'Select category...'}
                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="h-[250px] w-[300px] p-0">
                <Command>
                    <CommandInput placeholder={'Search...'} />
                    <CommandList>
                        <CommandEmpty>No category found.</CommandEmpty>
                        <CommandGroup>
                            {tags.map((tag) => (
                                <CommandItem
                                    key={tag.id}
                                    value={tag.id.toString()}
                                    onSelect={(currentValue) => {
                                        const value = parseInt(currentValue);
                                        if (selectedTags === undefined || selectedTags === null) {
                                            setSelected([value]);
                                            setSelectedTags([value]);
                                        } else {
                                            let selected;

                                            if (selectedTags.includes(value)) {
                                                selected = selectedTags.filter((val) => val !== value);
                                            } else {
                                                selected = [...selectedTags, value];
                                            }

                                            setSelected(selected);
                                            setSelectedTags(selected);
                                        }
                                    }}
                                >
                                    <Check
                                        className={cn(
                                            'mr-2 h-4 w-4',
                                            selectedTags?.includes(tag.id) ? 'opacity-100' : 'opacity-0',
                                        )}
                                    />
                                    {tag.name}
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
