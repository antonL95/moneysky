import screenshotDark from '@/../images/screenshots/dashboard_dark.jpg';
import screenshotLight from '@/../images/screenshots/dashboard_light.jpg';
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export default function () {
    const { auth } = usePage<SharedData>().props;
    return (
        <section className="bg-background relative min-h-screen overflow-hidden">
            {/* Background gradient effect */}
            <div className="from-chart-1/20 absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] via-transparent to-transparent" />

            {/* Content */}
            <div className="relative px-6 py-24 sm:py-32 lg:px-8">
                <div className="mx-auto max-w-7xl">
                    <div className="mx-auto max-w-2xl text-center">
                        <h1 className="from-foreground to-muted-foreground bg-gradient-to-r bg-clip-text text-5xl leading-normal font-bold tracking-tight text-transparent sm:text-6xl">
                            Automate your budgets
                        </h1>
                        <p className="text-muted-foreground text-lg leading-8">
                            Automate your finance tracking and stay informed with real-time updates.
                        </p>
                        <div className="mt-10 flex items-center justify-center gap-x-6">
                            {auth.user !== null ? (
                                <Button
                                    asChild
                                    className="bg-primary text-primary-foreground hover:bg-primary/90 px-8 py-6 text-lg"
                                >
                                    <Link href={route('dashboard')}>Go to the App</Link>
                                </Button>
                            ) : (
                                <Button
                                    asChild
                                    className="bg-primary text-primary-foreground hover:bg-primary/90 px-8 py-6 text-lg"
                                >
                                    <Link href={route('register')}>Get started</Link>
                                </Button>
                            )}
                        </div>
                    </div>

                    {/* Screenshot container with gradient border */}
                    <div className="mt-16 sm:mt-24">
                        <div className="relative">
                            {/* Gradient border effect */}
                            <div className="from-chart-1 to-chart-4 absolute -inset-0.5 rounded-2xl bg-gradient-to-r opacity-75 blur" />

                            {/* Screenshot */}
                            <div className="bg-card/90 relative rounded-xl p-2 backdrop-blur-sm lg:p-4">
                                <img
                                    src={screenshotDark}
                                    alt="App screenshot"
                                    width="2432"
                                    height="1442"
                                    className="hidden rounded-md shadow-2xl dark:block"
                                />
                                <img
                                    src={screenshotLight}
                                    alt="App screenshot"
                                    width="2432"
                                    height="1442"
                                    className="rounded-md shadow-2xl dark:hidden"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
