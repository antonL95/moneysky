import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import Budgets from '@/pages/dashboard/partials/budgets';
import BudgetForm from '@/pages/dashboard/partials/forms/budget-form';
import TransactionForm from '@/pages/dashboard/partials/forms/transaction-form';
import Investments from '@/pages/dashboard/partials/investments';
import Transactions from '@/pages/dashboard/partials/transactions';
import { Head, router, WhenVisible } from '@inertiajs/react';
import { useState } from 'react';
import TagData = App.Data.App.Dashboard.TagData;
import AssetData = App.Data.App.Dashboard.AssetData;
import HistoricalAssetsData = App.Data.App.Dashboard.HistoricalAssetsData;
import UserBudgetData = App.Data.App.Dashboard.UserBudgetData;
import TransactionAggregateData = App.Data.App.Dashboard.TransactionAggregateData;
import UserTransactionData = App.Data.App.Dashboard.UserTransactionData;
import UserManualEntryData = App.Data.App.ManualEntry.UserManualEntryData;

export default function Index({
    totalAssets,
    assets,
    historicalAssets,
    budgets,
    activeTab = 'investments',
    tags,
    currencies,
    transactionAggregates,
    transactions,
    userManualEntries,
    historicalDates,
    selectedDate,
}: {
    totalAssets: AssetData;
    assets: AssetData[];
    historicalAssets: HistoricalAssetsData[];
    budgets: UserBudgetData[];
    activeTab: string;
    tags: TagData[];
    currencies: { [key: string]: string };
    transactionAggregates: TransactionAggregateData[];
    transactions?: UserTransactionData[];
    userManualEntries: UserManualEntryData[];
    historicalDates: string[];
    selectedDate: string;
}) {
    const [open, setOpen] = useState(false);
    const [openTransaction, setOpenTransaction] = useState(false);

    return (
        <AppLayout>
            <Head title="Dashboard" />
            <Tabs defaultValue={activeTab} className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                <TabsList className="mx-auto grid w-full grid-cols-2 lg:max-w-md">
                    <TabsTrigger
                        value="investments"
                        onClick={() => {
                            if (activeTab === 'investments') {
                                return;
                            }
                            router.visit(route('dashboard', { activeTab: 'investments' }), {
                                preserveScroll: true,
                                preserveState: true,
                                only: ['totalAssets', 'assets', 'historicalAssets', 'activeTab'],
                            });
                        }}
                    >
                        Investments
                    </TabsTrigger>
                    <TabsTrigger
                        value="budget"
                        onClick={() => {
                            if (activeTab === 'budget') {
                                return;
                            }
                            router.visit(route('dashboard', { activeTab: 'budget' }), {
                                preserveScroll: true,
                                preserveState: true,
                                only: ['budgets', 'tags', 'transactionAggregates', 'activeTab', 'userManualEntries'],
                            });
                        }}
                    >
                        Budget
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="investments" className={`flex h-full w-full flex-1 flex-col gap-4`}>
                    <WhenVisible fallback={<SkeletonCard />} data={['totalAssets', 'assets', 'historicalAssets']}>
                        <Investments assets={assets} totalAssets={totalAssets} historicalAssets={historicalAssets} />
                    </WhenVisible>
                </TabsContent>
                <TabsContent value="budget" className={`flex h-full w-full flex-1 flex-col gap-4 rounded-xl`}>
                    <div className={'flex flex-col justify-between lg:flex-row'}>
                        <div>
                            <Select
                                onValueChange={(value) =>
                                    router.reload({
                                        data: { date: value },
                                        only: ['budgets', 'transactionAggregates', 'selectedDate'],
                                    })
                                }
                                value={selectedDate}
                            >
                                <SelectTrigger className="mb-6 w-full uppercase lg:w-[180px]">
                                    <SelectValue placeholder="Select date" />
                                </SelectTrigger>
                                <SelectContent className={`uppercase max-lg:landscape:h-48`}>
                                    <SelectGroup>
                                        {historicalDates.map((date, index) => (
                                            <SelectItem value={date} key={index}>
                                                {date}
                                            </SelectItem>
                                        ))}
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                        <div className={'flex flex-row flex-wrap justify-between gap-4'}>
                            <Button onClick={() => setOpen(true)}>Create budget</Button>
                            <Button onClick={() => setOpenTransaction(true)}>Add transaction</Button>
                        </div>
                    </div>
                    <WhenVisible fallback={<SkeletonCard />} data={['budgets', 'tags']}>
                        <Budgets budgets={budgets} tags={tags} currencies={currencies} />
                    </WhenVisible>
                    <WhenVisible fallback={<SkeletonCard />} data={['transactionAggregates']}>
                        <Transactions
                            transactionAggregates={transactionAggregates}
                            transactions={transactions}
                            currencies={currencies}
                            tags={tags}
                            userManualEntries={userManualEntries}
                        />
                    </WhenVisible>
                </TabsContent>
            </Tabs>

            <Dialog open={open} onOpenChange={() => setOpen(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add budget</DialogTitle>
                    </DialogHeader>
                    <BudgetForm tags={tags} closeModal={() => setOpen(false)} currencies={currencies} />
                </DialogContent>
            </Dialog>
            <Dialog open={openTransaction} onOpenChange={setOpenTransaction}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add transaction</DialogTitle>
                    </DialogHeader>
                    <TransactionForm
                        tags={tags}
                        closeModal={() => setOpenTransaction(false)}
                        currencies={currencies}
                        userManualWallets={userManualEntries}
                    />
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

export function SkeletonCard() {
    return (
        <div className="mx-auto flex min-w-full flex-col gap-8 md:h-[450px] lg:min-w-1/3">
            <Skeleton className="mx-auto h-4 w-sm" />
            <div className="h-full space-y-2">
                <Skeleton className="h-full w-full" />
            </div>
        </div>
    );
}
