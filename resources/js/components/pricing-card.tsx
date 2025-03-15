import { Button } from '@/components/ui/button';

export default function PricingCard({
    heading,
    features,
    price,
    subheading,
    cta,
    href,
}: {
    heading: string;
    features: string[];
    price: string;
    subheading: string;
    cta?: string;
    href?: string;
}) {
    return (
        <section
            className={
                'bg-background border-muted-foreground flex w-full flex-col rounded-3xl border px-6 py-8 sm:px-8 lg:w-96'
            }
        >
            <div className={'mb-6 flex flex-row items-center'}>
                <p className={'text-foreground mr-4 text-3xl font-light'}>{price}</p>
            </div>
            <h3 className={'text-foreground text-sm'}>{heading}</h3>
            <p className={'text-foreground text-sm'}>{subheading}</p>
            <Button className={'my-10'} asChild>
                <a href={href || route('subscribe')}>{cta}</a>
            </Button>
            <ul role="list" className={'text-foreground flex flex-col gap-y-3 text-sm'}>
                {features.map((feature) => (
                    <li key={feature} className="flex">
                        <span className={''}>{feature}</span>
                    </li>
                ))}
            </ul>
        </section>
    );
}
