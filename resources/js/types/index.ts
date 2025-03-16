import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: App.Data.App.UserData;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    url: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    auth: Auth;
    flash?: App.Data.App.FlashData;
    [key: string]: unknown;
}
