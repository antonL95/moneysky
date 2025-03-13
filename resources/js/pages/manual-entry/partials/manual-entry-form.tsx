import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import CurrencySelect from '@/components/ui/currency-select';
import { Loader2 } from 'lucide-react';
import UserManualEntryData = App.Data.App.ManualEntry.UserManualEntryData;
import ManualEntryData = App.Data.App.ManualEntry.ManualEntryData;
import { FormMessage } from '@/components/ui/form';

export default function ManualEntryForm({
    manualEntry,
    closeModal,
    currencies,
}: {
    manualEntry?: UserManualEntryData;
    closeModal: () => void;
    currencies: { [key: string]: string };
}) {
    const { data, setData, errors, post, put, processing, reset } = useForm<ManualEntryData>({
        name: manualEntry?.name || '',
        description: manualEntry?.description || '',
        balance: manualEntry?.amount || 0,
        currency: manualEntry?.currency || '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (manualEntry !== undefined) {
            put(
                route('manual-entry.update', {
                    manual_entry: manualEntry.id,
                }),
                {
                    onSuccess: () => {
                        setTimeout(() => {
                            reset();
                            closeModal();
                        }, 300);
                    },
                },
            );
        } else {
            post(route('manual-entry.store'), {
                onSuccess: () => {
                    setTimeout(() => {
                        reset();
                        closeModal();
                    }, 300);
                },
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
                <Label htmlFor={'description'}>Description</Label>
                <Textarea
                    id={'description'}
                    name={'description'}
                    value={data.description === null ? '' : data.description}
                    onChange={(e) => {
                        setData('description', e.target.value);
                    }}
                />
                {errors.description !== undefined && <FormMessage>{errors.description}</FormMessage>}
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
                <CurrencySelect
                    selected={manualEntry?.currency}
                    currencies={currencies}
                    setCurrency={(c) => {
                        setData('currency', c);
                    }}
                />
                {errors.currency !== undefined && <FormMessage>{errors.currency}</FormMessage>}
            </div>

            <div className="flex items-center gap-4">
                <Button type={'submit'} className={'mt-5'} disabled={processing}>
                    {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {manualEntry !== undefined ? 'Update' : 'Create'}
                </Button>
            </div>
        </form>
    );
}
