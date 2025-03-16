import PricingCard from '@/components/pricing-card';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export default function () {
    const { auth } = usePage<SharedData>().props;

    if (auth.user?.isSubscribed) {
        return <></>;
    }

    return (
        <div className="relative isolate px-6 py-24 sm:py-32 lg:px-8">
            <div className="mx-auto max-w-2xl text-center lg:max-w-4xl">
                <div className="space-y-3">
                    <h4 className="text-xl leading-none font-medium">Pricing</h4>
                    <p className="text-muted-foreground text-sm">The right price for you, whoever you are</p>
                </div>
            </div>
            <div className="mx-auto mt-16 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-20 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2 lg:gap-x-6">
                <PricingCard
                    heading={`Free`}
                    features={['Manage manual wallets', 'Manage budgets', 'Add manual transactions']}
                    price={`Free`}
                    subheading={'Manual management.'}
                    cta={'Get started'}
                />
                <PricingCard
                    heading={'Pro plan'}
                    features={[
                        'Everything in free',
                        'Sync unlimited bank accounts',
                        'Automatically sync transactions',
                        'Automatically track stock market tickers',
                        'Automatically track digital wallets',
                    ]}
                    price={'€4,99'}
                    subheading={'Unlock automated features with pro plan.'}
                    cta={'Get started'}
                />
            </div>
        </div>
    );
}
