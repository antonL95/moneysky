import Toast from '@/components/toast';
import { useToast } from '@/hooks/useToast';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ReactNode } from 'react';
import { SocialIcon } from 'react-social-icons';

interface AppLayoutProps {
    children: ReactNode;
}

export default function ({ children }: AppLayoutProps) {
    const flash = useToast();
    const { auth } = usePage<SharedData>().props;

    return (
        <div className="bg-background text-foreground flex min-h-screen flex-col items-center p-6 lg:justify-center lg:p-8">
            <header className="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
                <nav className="flex items-center justify-end gap-4">
                    {auth.user ? (
                        <Link
                            href={route('dashboard')}
                            className="border-muted text-foreground hover:border-foreground inline-block rounded-sm border px-5 py-1.5 text-sm leading-normal"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={route('login')}
                                className="text-foreground hover:border-foreground inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal"
                            >
                                Log in
                            </Link>
                            <Link
                                href={route('register')}
                                className="border-muted text-foreground hover:border-foreground inline-block rounded-sm border px-5 py-1.5 text-sm leading-normal"
                            >
                                Register
                            </Link>
                        </>
                    )}
                </nav>
            </header>

            <main
                className={`flex w-full flex-col items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0`}
            >
                {children}
                <Toast flash={flash} />
            </main>
            <footer className={`mt-10 w-full lg:mt-20`}>
                <div className="border-muted-foreground flex flex-col justify-center gap-8 border-t py-10">
                    <div className="flex flex-row items-center justify-center gap-x-4 text-center">
                        <SocialIcon
                            className={`size-7`}
                            url={`https://www.linkedin.com/company/moneysky-app`}
                            target="_blank"
                        />
                        <SocialIcon
                            className={`size-7`}
                            url={`https://www.facebook.com/profile.php?id=61564166234647`}
                            target={`_blank`}
                        />
                    </div>
                </div>
                <p className="text-foreground mt-6 space-x-2 text-center text-sm sm:mt-0">
                    <span>Copyright &copy; {new Date().getFullYear()} Moneysky. All rights reserved.</span>
                    <Link href={route('terms.show')}>Terms of service</Link>
                    <Link href={route('policy.show')}>Privacy policy</Link>
                </p>
            </footer>
        </div>
    );
}
