import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BitcoinIcon, ChartBar, LandmarkIcon, LayoutGrid, PiggyBankIcon, WalletCardsIcon } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: route(`dashboard`),
        icon: LayoutGrid,
    },
    {
        title: 'Bank Account',
        url: route(`bank-account.index`),
        icon: LandmarkIcon,
    },
    {
        title: 'Stock Market',
        url: route(`stock-market.index`),
        icon: ChartBar,
    },
    {
        title: 'Kraken account',
        url: route(`kraken-account.index`),
        icon: BitcoinIcon,
    },
    {
        title: 'Digital wallet',
        url: route(`digital-wallet.index`),
        icon: WalletCardsIcon,
    },
    {
        title: 'Manual wallet',
        url: route(`manual-entry.index`),
        icon: PiggyBankIcon,
    },
];

const footerNavItems: NavItem[] = [];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="floating">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={route(`home`)} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
