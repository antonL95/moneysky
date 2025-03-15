import screenshotDark from '@/../images/screenshots/dashboard_dark.jpg';
import screenshotLight from '@/../images/screenshots/dashboard_light.jpg';
import screenshotMobileDark from '@/../images/screenshots/dashboard_mobile_dark.jpg';
import screenshotMobileLight from '@/../images/screenshots/dashboard_mobile_light.jpg';
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export default function () {
    const { auth } = usePage<SharedData>().props;
    return (
        <section>
            <div className="relative isolate dark:hidden">
                <div className="py-24 sm:py-32 lg:pb-40">
                    <div className="mx-auto max-w-7xl px-6 lg:px-8">
                        <div className="mx-auto max-w-2xl text-center">
                            <h1 className="text-foreground text-4xl font-bold tracking-tight text-balance sm:text-6xl">
                                Automate your budgets
                            </h1>
                            <p className="mt-6 text-lg leading-8 text-gray-600">
                                Automate your finance tracking and stay informed with real-time updates.
                            </p>
                            <div className="mt-10 flex items-center justify-center gap-x-6">
                                {auth.user !== null ? (
                                    <Button asChild>
                                        <Link href={route('dashboard')}>Go to the App</Link>
                                    </Button>
                                ) : (
                                    <Button asChild>
                                        <Link href={route('register')}>Get started</Link>
                                    </Button>
                                )}
                            </div>
                        </div>

                        <div className="mt-16 sm:mt-24 lg:mt-0 lg:hidden lg:shrink-0 lg:grow">
                            <svg
                                viewBox="0 0 366 729"
                                role="img"
                                className="mx-auto w-[22.875rem] max-w-full drop-shadow-xl"
                            >
                                <title>App screenshot</title>
                                <defs>
                                    <clipPath id="2ade4387-9c63-4fc4-b754-10e687a0d332">
                                        <rect width="316" height="684" rx="36" />
                                    </clipPath>
                                </defs>
                                <path
                                    fill="#4B5563"
                                    d="M363.315 64.213C363.315 22.99 341.312 1 300.092 1H66.751C25.53 1 3.528 22.99 3.528 64.213v44.68l-.857.143A2 2 0 0 0 1 111.009v24.611a2 2 0 0 0 1.671 1.973l.95.158a2.26 2.26 0 0 1-.093.236v26.173c.212.1.398.296.541.643l-1.398.233A2 2 0 0 0 1 167.009v47.611a2 2 0 0 0 1.671 1.973l1.368.228c-.139.319-.314.533-.511.653v16.637c.221.104.414.313.56.689l-1.417.236A2 2 0 0 0 1 237.009v47.611a2 2 0 0 0 1.671 1.973l1.347.225c-.135.294-.302.493-.49.607v377.681c0 41.213 22 63.208 63.223 63.208h95.074c.947-.504 2.717-.843 4.745-.843l.141.001h.194l.086-.001 33.704.005c1.849.043 3.442.37 4.323.838h95.074c41.222 0 63.223-21.999 63.223-63.212v-394.63c-.259-.275-.48-.796-.63-1.47l-.011-.133 1.655-.276A2 2 0 0 0 366 266.62v-77.611a2 2 0 0 0-1.671-1.973l-1.712-.285c.148-.839.396-1.491.698-1.811V64.213Z"
                                />
                                <path
                                    fill="#343E4E"
                                    d="M16 59c0-23.748 19.252-43 43-43h246c23.748 0 43 19.252 43 43v615c0 23.196-18.804 42-42 42H58c-23.196 0-42-18.804-42-42V59Z"
                                />
                                <foreignObject
                                    width="316"
                                    height="684"
                                    transform="translate(24 24)"
                                    clip-path="url(#2ade4387-9c63-4fc4-b754-10e687a0d332)"
                                >
                                    <img src={screenshotMobileLight} className="h-full" alt="" />
                                </foreignObject>
                            </svg>
                        </div>
                        <div className="mt-16 flow-root max-lg:hidden sm:mt-24">
                            <div className="-m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-gray-900/10 ring-inset lg:-m-4 lg:rounded-2xl lg:p-4">
                                <img
                                    src={screenshotLight}
                                    alt="App screenshot"
                                    width="2432"
                                    height="1442"
                                    className="rounded-md shadow-2xl ring-1 ring-gray-900/10"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="relative isolate hidden dark:block">
                <div className="py-24 sm:py-32 lg:pb-40">
                    <div className="mx-auto max-w-7xl px-6 lg:px-8">
                        <div className="mx-auto max-w-2xl text-center">
                            <h1 className="text-foreground text-4xl font-bold tracking-tight text-balance sm:text-6xl">
                                Automate your budgets
                            </h1>
                            <p className="mt-6 text-lg leading-8 text-gray-300">
                                Automate your finance tracking and stay informed with real-time updates.
                            </p>
                            <div className="mt-10 flex items-center justify-center gap-x-6">
                                {auth.user !== null ? <Button>Go to the App</Button> : <Button>Get started</Button>}
                            </div>
                        </div>
                        <div className="mt-16 sm:mt-24 lg:mt-0 lg:hidden lg:shrink-0 lg:grow">
                            <svg
                                viewBox="0 0 366 729"
                                role="img"
                                className="mx-auto w-[22.875rem] max-w-full drop-shadow-xl"
                            >
                                <title>App screenshot</title>
                                <defs>
                                    <clipPath id="2ade4387-9c63-4fc4-b754-10e687a0d3323">
                                        <rect width="316" height="684" rx="36" />
                                    </clipPath>
                                </defs>
                                <path
                                    fill="#4B5563"
                                    d="M363.315 64.213C363.315 22.99 341.312 1 300.092 1H66.751C25.53 1 3.528 22.99 3.528 64.213v44.68l-.857.143A2 2 0 0 0 1 111.009v24.611a2 2 0 0 0 1.671 1.973l.95.158a2.26 2.26 0 0 1-.093.236v26.173c.212.1.398.296.541.643l-1.398.233A2 2 0 0 0 1 167.009v47.611a2 2 0 0 0 1.671 1.973l1.368.228c-.139.319-.314.533-.511.653v16.637c.221.104.414.313.56.689l-1.417.236A2 2 0 0 0 1 237.009v47.611a2 2 0 0 0 1.671 1.973l1.347.225c-.135.294-.302.493-.49.607v377.681c0 41.213 22 63.208 63.223 63.208h95.074c.947-.504 2.717-.843 4.745-.843l.141.001h.194l.086-.001 33.704.005c1.849.043 3.442.37 4.323.838h95.074c41.222 0 63.223-21.999 63.223-63.212v-394.63c-.259-.275-.48-.796-.63-1.47l-.011-.133 1.655-.276A2 2 0 0 0 366 266.62v-77.611a2 2 0 0 0-1.671-1.973l-1.712-.285c.148-.839.396-1.491.698-1.811V64.213Z"
                                />
                                <path
                                    fill="#343E4E"
                                    d="M16 59c0-23.748 19.252-43 43-43h246c23.748 0 43 19.252 43 43v615c0 23.196-18.804 42-42 42H58c-23.196 0-42-18.804-42-42V59Z"
                                />
                                <foreignObject
                                    width="316"
                                    height="684"
                                    transform="translate(24 24)"
                                    clip-path="url(#2ade4387-9c63-4fc4-b754-10e687a0d3323)"
                                >
                                    <img src={screenshotMobileDark} className="h-full" alt="" />
                                </foreignObject>
                            </svg>
                        </div>
                        <img
                            src={screenshotDark}
                            alt="App screenshot"
                            width="2432"
                            height="1442"
                            className="mt-16 rounded-md bg-white/5 shadow-2xl ring-1 ring-white/10 max-lg:hidden sm:mt-24"
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
