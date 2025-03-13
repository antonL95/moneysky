import { Head } from '@inertiajs/react';
import PricingCard from '@/components/pricing-card';
import React from 'react';
import GuestLayout from '@/layouts/guest-layout';

export default function Subscribe() {
    return (
        <GuestLayout>
            <Head title="Subscribe" />
            <div className={'flex w-full grow flex-col items-center justify-center'}>
                <div className="w-full items-center pt-6 sm:justify-center sm:pt-0 md:max-w-md">
                    <div className={'my-auto w-full'}>
                        <div>
                            <PricingCard
                                heading={'Pro plan'}
                                subheading={'Unlock automated features with pro plan.'}
                                price={'€4,99'}
                                cta={'Try for free for 14 days'}
                                features={[
                                    'Everything in free',
                                    'Sync unlimited bank accounts',
                                    'Automatically sync transactions',
                                    'Automatically track stock market tickers',
                                    'Automatically track digital wallets',
                                ]}
                                href={route('stripe.subscription-checkout')}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </GuestLayout>
    );
}
