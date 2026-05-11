import type { InertiaLinkProps } from '@inertiajs/react'
import type { LucideIcon } from 'lucide-react'
import type { ReactNode } from 'react'
import type { ComponentType } from 'react';

// existing — jangan diubah
export type BreadcrumbItem = {
    title: string
    href:  NonNullable<InertiaLinkProps['href']>
}

export interface NavItem {
    title:  string;
    href:   string;
    icon?:  ComponentType<{ className?: string }>;
    /** Spatie / custom permission name. null = selalu tampil */
    permission?: string | null;
}

// ─── Sidebar menu (Web Arsip) ─────────────────────────────────

export type SidebarMenuItem = {
    name:       string
    route:      string
    permission: string | null   // null = selalu tampil (dashboard)
    icon:       ReactNode
}

export type SidebarMenuGroup = {
    label: string
    items: SidebarMenuItem[]
}