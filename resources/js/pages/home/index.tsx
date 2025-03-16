import GuestLayout from '@/layouts/guest-layout';
import BanksMarquee from '@/pages/home/partials/banks-marquee';
import Hero from '@/pages/home/partials/hero';
import Pricing from '@/pages/home/partials/pricing';
import Security from '@/pages/home/partials/security';
import { WhenVisible } from '@inertiajs/react';
import BankInstitutionData = App.Data.App.BankAccount.BankInstitutionData;

export default function ({ banks }: { banks: Array<{ [key: number | string]: BankInstitutionData }> }) {
    return (
        <GuestLayout>
            <Hero />
            <Security />
            <WhenVisible fallback={<div>Loading...</div>} data={['banks']}>
                <BanksMarquee banks={banks} />
            </WhenVisible>
            <Pricing />
        </GuestLayout>
    );
}
