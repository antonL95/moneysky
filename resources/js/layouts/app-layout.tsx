import { useToast } from '@/hooks/useToast';
import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
}

export default function ({ children, ...props }: AppLayoutProps) {
    const flash = useToast();
    return (
        <AppLayoutTemplate flash={flash} {...props}>
            {children}
        </AppLayoutTemplate>
    );
}
