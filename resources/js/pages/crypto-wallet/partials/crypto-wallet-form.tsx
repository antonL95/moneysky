import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useForm } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { FormEventHandler } from 'react';
import CryptoWalletData = App.Data.App.CryptoWallet.CryptoWalletData;
import { FormMessage } from '@/components/ui/form';
import UserCryptoWalletData = App.Data.App.CryptoWallet.UserCryptoWalletData;

const chains = [
    { value: 'eth', name: 'ETH' },
    { value: 'matic', name: 'Polygon (matic)' },
    { value: 'btc', name: 'Bitcoin' },
] as { value: App.Enums.ChainType; name: string }[];

export default function CryptoWalletForm({ wallet, closeModal }: { wallet?: UserCryptoWalletData; closeModal: () => void }) {
    const { data, setData, errors, post, put, processing, reset } = useForm<CryptoWalletData>({
        address: wallet?.walletAddress || '',
        chainType: wallet?.chainType || 'btc',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (wallet !== undefined) {
            put(
                route('digital-wallet.update', {
                    digital_wallet: wallet.id,
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
            post(route('digital-wallet.store'), {
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
                <Label htmlFor={'address'}>Wallet Address</Label>
                <Input
                    id={'address'}
                    name={'address'}
                    value={data.address}
                    onChange={(e) => {
                        setData('address', e.target.value);
                    }}
                    autoFocus={true}
                />
                {errors.address !== undefined && <FormMessage>{errors.address}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <Select
                    name={'chain'}
                    value={data.chainType}
                    onValueChange={(value: App.Enums.ChainType) => {
                        setData('chainType', value);
                    }}
                >
                    <SelectTrigger className="w-full">
                        <SelectValue placeholder="Select a chain..." />
                    </SelectTrigger>
                    <SelectContent>
                        {chains.map((chain) => (
                            <SelectItem value={chain.value}>{chain.name}</SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                {errors.chainType !== undefined && <FormMessage>{errors.chainType}</FormMessage>}
            </div>

            <div className="flex items-center gap-4">
                <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                    {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {wallet !== undefined ? 'Update' : 'Create'}
                </Button>
            </div>
        </form>
    );
}
