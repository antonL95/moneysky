import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import Toast from '@/components/toast';
import { ReactNode } from 'react';

export default function AppSidebarLayout({ children, flash }: { children: ReactNode; flash?: App.Data.App.FlashData }) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar">
                <AppSidebarHeader />
                {children}
            </AppContent>
            <Toast flash={flash} />
        </AppShell>
    );
}
