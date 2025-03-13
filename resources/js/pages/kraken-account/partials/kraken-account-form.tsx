import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Loader2 } from 'lucide-react';
import UserKrakenAccountData = App.Data.App.KrakenAccount.UserKrakenAccountData;
import KrakenAccountData = App.Data.App.KrakenAccount.KrakenAccountData;
import { FormMessage } from '@/components/ui/form';

export default function KrakenAccountForm({
    krakenAccount,
    closeModal,
}: {
    krakenAccount?: UserKrakenAccountData;
    closeModal: () => void;
}) {
    const { data, setData, errors, post, put, processing, reset } = useForm<KrakenAccountData>({
        privateKey: krakenAccount?.privateKey || '',
        apiKey: krakenAccount?.apiKey || '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (krakenAccount !== undefined) {
            put(
                route('kraken-account.update', {
                    kraken_account: krakenAccount.id,
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
            post(route('kraken-account.store'), {
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
                <Label htmlFor={'apiKey'}>Api Key</Label>
                <Input
                    id={'apiKey'}
                    name={'apiKey'}
                    value={data.apiKey}
                    onChange={(e) => {
                        setData('apiKey', e.target.value);
                    }}
                    autoFocus={true}
                />
                {errors.apiKey !== undefined && <FormMessage>{errors.apiKey}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <Label htmlFor={'privateKey'}>Private Key</Label>
                <Input
                    id={'privateKey'}
                    name={'privateKey'}
                    value={data.privateKey}
                    onChange={(e) => {
                        setData('privateKey', e.target.value);
                    }}
                />
                {errors.privateKey !== undefined && <FormMessage>{errors.privateKey}</FormMessage>}
            </div>

            <div className="flex items-center gap-4">
                <Button type={'submit'} className={'mt-5'} disabled={processing}>
                    {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {krakenAccount !== undefined ? 'Update' : 'Create'}
                </Button>
            </div>
        </form>
    );
}
