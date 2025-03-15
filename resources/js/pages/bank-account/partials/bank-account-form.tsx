import { Button } from '@/components/ui/button';
import { FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { FormEventHandler } from 'react';
import UserBankAccountData = App.Data.App.BankAccount.UserBankAccountData;
import BankAccountData = App.Data.App.BankAccount.BankAccountData;

export default function BankAccountForm({
    bankAccount,
    closeModal,
}: {
    bankAccount: UserBankAccountData;
    closeModal: () => void;
}) {
    const { data, setData, errors, put, processing, reset } = useForm<BankAccountData>({
        name: bankAccount.name || '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        put(
            route('bank-account.update', {
                bank_account: bankAccount.id,
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
    };

    return (
        <form onSubmit={submit} className="mt-6 space-y-6">
            <div className="grid gap-2">
                <Label>Name</Label>
                <Input
                    name={'name'}
                    value={data.name === null ? undefined : data.name}
                    onChange={(e) => {
                        setData('name', e.target.value);
                    }}
                    autoFocus={true}
                />
                {errors.name !== undefined && <FormMessage>{errors.name}</FormMessage>}
            </div>

            <div className="flex items-center gap-4">
                <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                    {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    Update
                </Button>
            </div>
        </form>
    );
}
