import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import Budgets from '@/pages/dashboard/partials/budgets';
import Investments from '@/pages/dashboard/partials/investments';
import { Head, router, WhenVisible } from '@inertiajs/react';
import TagData = App.Data.App.Dashboard.TagData;
import AssetData = App.Data.App.Dashboard.AssetData;
import HistoricalAssetsData = App.Data.App.Dashboard.HistoricalAssetsData;
import UserBudgetData = App.Data.App.Dashboard.UserBudgetData;

export default function Index({
    totalAssets,
    assets,
    historicalAssets,
    budgets,
    activeTab = 'investments',
    tags,
    currencies,
}: {
    totalAssets: AssetData;
    assets: AssetData[];
    historicalAssets: HistoricalAssetsData[];
    budgets: UserBudgetData[];
    activeTab: string;
    tags: TagData[];
    currencies: { [key: string]: string };
}) {
    return (
        <AppLayout>
            <Head title="Dashboard" />
            <Tabs defaultValue={activeTab} className={`flex h-full flex-1 flex-col gap-4 rounded-xl p-4`}>
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
                                only: ['budgets', 'tags', 'activeTab'],
                            });
                        }}
                    >
                        Budget
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="investments" className={`flex h-full flex-1 flex-col gap-4`}>
                    <WhenVisible fallback={<SkeletonCard />} data={['totalAssets', 'assets', 'historicalAssets']}>
                        <Investments assets={assets} totalAssets={totalAssets} historicalAssets={historicalAssets} />
                    </WhenVisible>
                </TabsContent>
                <TabsContent value="budget" className={`flex h-full flex-1 flex-col gap-4 rounded-xl`}>
                    <WhenVisible fallback={<SkeletonCard />} data={['budgets', 'tags']}>
                        <Budgets budgets={budgets} tags={tags} currencies={currencies} />
                    </WhenVisible>
                </TabsContent>
            </Tabs>
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
