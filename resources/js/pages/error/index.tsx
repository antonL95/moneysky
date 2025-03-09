import GuestLayout from '@/layouts/guest-layout';
import { Head } from '@inertiajs/react';

export default function ({ status }: { status: number }) {
    const title = {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[status];

    const description = {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[status];

    return (
        <GuestLayout>
            <Head title={title} />
            <div className="flex min-h-screen flex-col items-center pt-5 text-black sm:pt-12">
                <h1 className={'text-center text-4xl'}>{title}</h1>
                <p className={'text-center text-lg'}>{description}</p>
            </div>
        </GuestLayout>
    );
}
