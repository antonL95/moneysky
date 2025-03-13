import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Loader2 } from 'lucide-react';
import StockMarketData = App.Data.App.StockMarket.StockMarketData;
import UserStockMarketData = App.Data.App.StockMarket.UserStockMarketData;
import { FormMessage } from '@/components/ui/form';

export default function StockMarketForm({
    stockMarket,
    closeModal,
}: {
    stockMarket?: UserStockMarketData;
    closeModal: () => void;
}) {
    const { data, setData, errors, post, put, processing, reset } = useForm<StockMarketData>({
        ticker: stockMarket?.ticker || '',
        amount: stockMarket?.amount || 0,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (stockMarket !== undefined) {
            put(
                route('stock-market.update', {
                    stock_market: stockMarket.id,
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
            post(route('stock-market.store'), {
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
                <Label htmlFor={'ticker'}>Ticker</Label>
                <Input
                    id={'ticker'}
                    name={'ticker'}
                    value={data.ticker}
                    onChange={(e) => {
                        setData('ticker', e.target.value);
                    }}
                    autoFocus={true}
                />
                {errors.ticker !== undefined && <FormMessage>{errors.ticker}</FormMessage>}
            </div>
            <div className="grid gap-2">
                <Label htmlFor={'amount'}>Amount</Label>
                <Input
                    id={'amount'}
                    name={'amount'}
                    value={data.amount}
                    type={'number'}
                    step={'0.001'}
                    onChange={(e) => {
                        setData('amount', Number.parseFloat(e.target.value));
                    }}
                />
                {errors.amount !== undefined && <FormMessage>{errors.amount}</FormMessage>}
            </div>

            <div className="flex items-center gap-4">
                <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                    {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {stockMarket !== undefined ? 'Update' : 'Create'}
                </Button>
            </div>
        </form>
    );
}
