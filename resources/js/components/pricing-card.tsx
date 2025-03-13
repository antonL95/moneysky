import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

export default function PricingCard({
    heading,
    features,
    price,
    subheading,
    cta,
    badge,
    href,
}: {
    heading: string;
    features: string[];
    price: string;
    subheading: string;
    cta?: string;
    badge?: string;
    href?: string;
}) {
    return (
        <section className={'flex w-full flex-col rounded-3xl bg-blue-600 px-6 py-8 sm:px-8 lg:w-96'}>
            <div className={'mb-6 flex flex-row items-center'}>
                <p className={'mr-4 text-3xl font-light text-white'}>{price}</p>
                {badge && (
                    <Badge color={'orange'} className={'bg-amber-700 text-white'}>
                        {badge}
                    </Badge>
                )}
            </div>
            <h3 className={'text-sm text-white'}>{heading}</h3>
            <p className={'text-sm text-white'}>{subheading}</p>
            <Button className={'my-10'} asChild>
                <a href={href || route('subscribe')}>{cta}</a>
            </Button>
            <ul role="list" className={'flex flex-col gap-y-3 text-sm text-white'}>
                {features.map((feature) => (
                    <li key={feature} className="flex">
                        <span className={''}>{feature}</span>
                    </li>
                ))}
            </ul>
        </section>
    );
}
