import { Button } from '@/components/ui/button';
import CurrencySelect from '@/components/ui/currency-select';
import { FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { FormEventHandler } from 'react';
import TransactionData = App.Data.App.Dashboard.TransactionData;
import UserTransactionData = App.Data.App.Dashboard.UserTransactionData;
import UserManualEntryData = App.Data.App.ManualEntry.UserManualEntryData;
import TagData = App.Data.App.Dashboard.TagData;

export default function ({
    transaction,
    tags,
    closeModal,
    currencies,
    userManualWallets,
}: {
    transaction?: UserTransactionData;
    tags: TagData[];
    closeModal: () => void;
    currencies: { [key: string]: string };
    userManualWallets: UserManualEntryData[];
}) {
    const { data, setData, errors, post, put, processing, reset } = useForm<TransactionData>({
        transaction_tag_id: transaction?.transactionTagId || (null as number | null),
        balance: transaction?.amount || (0 as number),
        currency: transaction?.currency || ('' as string),
        description: transaction?.description || ('' as string),
        user_manual_entry_id: transaction?.userManualEntryId || (null as number | null),
    });
    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        if (transaction === undefined) {
            post(route('spending.transaction.store'), {
                onSuccess: () => {
                    setTimeout(() => {
                        reset();
                        closeModal();
                    }, 100);
                },
                except: ['totalAssets', 'assets', 'historicalAssets', 'activeTab'],
            });
        } else {
            put(
                route('spending.transaction.update', {
                    user_transaction: transaction.id,
                }),
                {
                    onSuccess: () => {
                        setTimeout(() => {
                            reset();
                            closeModal();
                        }, 100);
                    },
                    except: ['totalAssets', 'assets', 'historicalAssets', 'activeTab'],
                },
            );
        }
    };

    return (
        <form onSubmit={submit} className="mt-6 space-y-6">
            <div className="grid gap-2">
                <Label htmlFor={'balance'}>Amount</Label>
                <Input
                    disabled={transaction?.transactionType === 'automatic'}
                    id={'balance'}
                    name={'balance'}
                    value={data.balance}
                    type={'number'}
                    step={'0.001'}
                    onChange={(e) => {
                        setData('balance', parseFloat(e.target.value));
                    }}
                    autoFocus={true}
                />
                {errors.balance !== undefined && <FormMessage>{errors.balance}</FormMessage>}
            </div>
            {transaction?.transactionType !== 'automatic' && (
                <div className="grid gap-2">
                    <CurrencySelect
                        selected={data.currency}
                        currencies={currencies}
                        setCurrency={(c) => {
                            setData('currency', c);
                        }}
                    />
                    {errors.currency !== undefined && <FormMessage>{errors.currency}</FormMessage>}
                </div>
            )}
            <div className="grid gap-2">
                <Label htmlFor={'description'}>Description</Label>
                <Textarea
                    disabled={transaction?.transactionType === 'automatic'}
                    value={data.description || ''}
                    id={'description'}
                    name={'description'}
                    onChange={(e) => {
                        setData('description', e.target.value);
                    }}
                />
                {errors.description !== undefined && <FormMessage>{errors.description}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <Select
                    name={'transaction_tag_id'}
                    onValueChange={(e) => {
                        setData('transaction_tag_id', e === '' ? null : parseInt(e));
                    }}
                    defaultValue={data.transaction_tag_id?.toString()}
                >
                    <SelectTrigger className="w-full">
                        <SelectValue placeholder="Select a category..." />
                    </SelectTrigger>
                    <SelectContent>
                        {tags.length > 0 &&
                            tags.map((tag) => (
                                <SelectItem value={tag.id.toString()} key={tag.id}>
                                    {tag.name}
                                </SelectItem>
                            ))}
                    </SelectContent>
                </Select>
                {errors.transaction_tag_id !== undefined && <FormMessage>{errors.transaction_tag_id}</FormMessage>}
            </div>
            {transaction?.transactionType !== 'automatic' && (
                <div className="grid gap-2">
                    <Select
                        name={'user_manual_entry_id'}
                        onValueChange={(e) => {
                            setData('user_manual_entry_id', e === '' ? null : parseInt(e));
                        }}
                        defaultValue={data.user_manual_entry_id?.toString()}
                    >
                        <SelectTrigger className="w-full">
                            <SelectValue placeholder="Select a wallet..." />
                        </SelectTrigger>
                        <SelectContent>
                            {userManualWallets.length > 0 &&
                                userManualWallets.map((wallet) => (
                                    <SelectItem value={wallet.id.toString()} key={wallet.id}>
                                        {wallet.name}
                                    </SelectItem>
                                ))}
                        </SelectContent>
                    </Select>
                    {errors.user_manual_entry_id !== undefined && (
                        <FormMessage>{errors.user_manual_entry_id}</FormMessage>
                    )}
                </div>
            )}

            <div className="flex items-center gap-4">
                <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                    {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {transaction !== undefined ? 'Update' : 'Create'}
                </Button>
            </div>
        </form>
    );
}
