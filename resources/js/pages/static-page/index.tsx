import GuestLayout from '@/layouts/guest-layout';
import { Head } from '@inertiajs/react';

export default function Index({ content, title }: { content: TrustedHTML; title: string }) {
    return (
        <GuestLayout>
            <Head title={title} />
            <div className="flex min-h-screen flex-col items-center pt-5 sm:pt-12">
                <div
                    className="prose-zinc lg:prose-xl prose-a:text-blue-600 dark:text text-gray-900 sm:max-w-4xl"
                    dangerouslySetInnerHTML={{ __html: content }}
                />
            </div>
        </GuestLayout>
    );
}
