import type { ReactNode } from 'react'
import type { BreadcrumbItem } from '@/types/navigation'

// existing — jangan diubah
export type AppLayoutProps = {
    children:     ReactNode
    breadcrumbs?: BreadcrumbItem[]
}

export type AppVariant = 'header' | 'sidebar'

export type AuthLayoutProps = {
    children?:    ReactNode
    name?:        string
    title?:       string
    description?: string
}

// ─── Flash / Toast ────────────────────────────────────────────

export type FlashToast = {
    type:    'success' | 'info' | 'warning' | 'error'
    message: string
}

// Alias yang selaras dengan props flash dari Inertia
export type FlashMessage = {
    success: string | null
    error:   string | null
}