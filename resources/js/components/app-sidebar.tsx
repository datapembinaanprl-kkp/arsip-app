import React, { useState, useEffect, createContext, useContext } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    LayoutDashboard, FileText, Users, Settings, ChevronLeft,
    ChevronRight, FolderOpen, Activity, Menu, X, ChevronDown,
    ClipboardList,
} from 'lucide-react';
import { useAuth } from '@/hooks/use-auth';

// ─── Types ────────────────────────────────────────────────────────────────────

interface NavChild {
    label:      string;
    href:       string;
    permission: string | null;
}

interface NavItem {
    label:      string;
    icon:       React.ComponentType<{ className?: string }>;
    href?:      string;
    children?:  NavChild[];
    permission: string | null; // null = selalu tampil
}

interface SidebarContextValue {
    collapsed:     boolean;
    setCollapsed:  (v: boolean) => void;
    mobileOpen:    boolean;
    setMobileOpen: (v: boolean) => void;
}

// ─── Context ──────────────────────────────────────────────────────────────────

const SidebarContext = createContext<SidebarContextValue>({
    collapsed:     false,
    setCollapsed:  () => {},
    mobileOpen:    false,
    setMobileOpen: () => {},
});

export function useSidebarContext() {
    return useContext(SidebarContext);
}

// ─── Nav Config ───────────────────────────────────────────────────────────────

const NAV_ITEMS: NavItem[] = [
    {
        label:      'Dashboard',
        icon:       LayoutDashboard,
        href:       '/dashboard',
        permission: null,
    },
    {
        label:      'Dokumen',
        icon:       FileText,
        permission: 'documents.view',
        children: [
            { label: 'Semua Dokumen',  href: '/documents',        permission: 'documents.view'   },
            { label: 'Tambah Dokumen', href: '/documents/create', permission: 'documents.create' },
        ],
    },
    {
        label:      'Tim Kerja',
        icon:       FolderOpen,
        href:       '/tim-kerja',
        permission: 'tim-kerja.view',
    },
    {
        label:      'Pengguna',
        icon:       Users,
        href:       '/users',
        permission: 'users.view',
    },
    {
        label:      'Log Aktivitas',
        icon:       Activity,
        href:       '/activity-logs',
        permission: 'activity-logs.view',
    },
    {
        label:      'Pengaturan',
        icon:       Settings,
        href:       '/settings',
        permission: 'settings.manage',
    },
];

// ─── Helpers ──────────────────────────────────────────────────────────────────

function isActive(href: string, currentUrl: string): boolean {
    if (href === '/') return currentUrl === '/';
    return currentUrl.startsWith(href);
}

// ─── Nav Item ─────────────────────────────────────────────────────────────────

function SidebarNavItem({
    item,
    collapsed,
    currentUrl,
}: {
    item:       NavItem;
    collapsed:  boolean;
    currentUrl: string;
}) {
    const { can } = useAuth();

    // Permission check
    if (item.permission !== null && !can(item.permission)) return null;

    const hasChildren  = !!item.children?.length;
    const isItemActive = hasChildren
        ? item.children?.some(c => isActive(c.href, currentUrl))
        : item.href ? isActive(item.href, currentUrl) : false;

    const [expanded, setExpanded] = useState(!!isItemActive);
    const Icon = item.icon;

    const base     = 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 w-full';
    const active   = 'bg-blue-600 text-white shadow-sm';
    const inactive = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';

    // ── Group with children ──
    if (hasChildren) {
        const visibleChildren = item.children!.filter(
            c => c.permission === null || can(c.permission)
        );
        if (visibleChildren.length === 0) return null;

        return (
            <div>
                <button
                    onClick={() => !collapsed && setExpanded(v => !v)}
                    title={collapsed ? item.label : undefined}
                    className={`${base} ${isItemActive && collapsed ? active : inactive}`}
                >
                    <Icon className={`w-5 h-5 flex-shrink-0 ${isItemActive && collapsed ? 'text-white' : 'text-gray-500'}`} />
                    {!collapsed && (
                        <>
                            <span className="flex-1 text-left">{item.label}</span>
                            <ChevronDown className={`w-4 h-4 text-gray-400 transition-transform duration-200 ${expanded ? 'rotate-180' : ''}`} />
                        </>
                    )}
                </button>

                {!collapsed && expanded && (
                    <div className="ml-4 mt-1 pl-4 border-l-2 border-gray-100 space-y-0.5">
                        {visibleChildren.map(child => {
                            const childActive = isActive(child.href, currentUrl);
                            return (
                                <Link
                                    key={child.href}
                                    href={child.href}
                                    className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors ${
                                        childActive
                                            ? 'text-blue-600 font-medium bg-blue-50'
                                            : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50'
                                    }`}
                                >
                                    <span className={`w-1.5 h-1.5 rounded-full flex-shrink-0 ${childActive ? 'bg-blue-500' : 'bg-gray-300'}`} />
                                    {child.label}
                                </Link>
                            );
                        })}
                    </div>
                )}
            </div>
        );
    }

    // ── Single link ──
    return (
        <Link
            href={item.href!}
            title={collapsed ? item.label : undefined}
            className={`${base} ${isItemActive ? active : inactive}`}
        >
            <Icon className={`w-5 h-5 flex-shrink-0 ${isItemActive ? 'text-white' : 'text-gray-500'}`} />
            {!collapsed && <span>{item.label}</span>}
        </Link>
    );
}

// ─── Sidebar Inner ────────────────────────────────────────────────────────────

function SidebarInner({
    collapsed,
    onToggle,
    onClose,
    showClose = false,
}: {
    collapsed:  boolean;
    onToggle:   () => void;
    onClose?:   () => void;
    showClose?: boolean;
}) {
    const { url } = usePage();
    const { user, role } = useAuth();

    return (
        <div
            className={`
                flex flex-col h-full bg-white border-r border-gray-200
                transition-[width] duration-300 ease-in-out overflow-hidden
                ${collapsed ? 'w-[68px]' : 'w-64'}
            `}
        >
            {/* ── Brand ── */}
            <div className={`flex items-center gap-3 px-4 py-4 border-b border-gray-100 flex-shrink-0 ${collapsed ? 'justify-center' : 'justify-between'}`}>
                {!collapsed ? (
                    <div className="flex items-center gap-2.5 min-w-0">
                        <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                            <FileText className="w-4 h-4 text-white" />
                        </div>
                        <div className="min-w-0">
                            <p className="font-bold text-gray-900 text-sm leading-tight truncate">Web Arsip</p>
                            <p className="text-[10px] text-gray-400 leading-tight mt-0.5 truncate">Sistem Arsip Digital</p>
                        </div>
                    </div>
                ) : (
                    <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                        <FileText className="w-4 h-4 text-white" />
                    </div>
                )}

                {showClose ? (
                    <button onClick={onClose} className="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 flex-shrink-0">
                        <X className="w-4 h-4" />
                    </button>
                ) : (
                    !collapsed && (
                        <button onClick={onToggle} className="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 flex-shrink-0 transition-colors">
                            <ChevronLeft className="w-4 h-4" />
                        </button>
                    )
                )}
            </div>

            {/* ── Collapsed toggle (shown at bottom of icon) ── */}
            {collapsed && !showClose && (
                <button
                    onClick={onToggle}
                    className="flex items-center justify-center py-2 hover:bg-gray-50 text-gray-400 flex-shrink-0"
                >
                    <ChevronRight className="w-4 h-4" />
                </button>
            )}

            {/* ── Nav ── */}
            <nav className="flex-1 overflow-y-auto px-2 py-2 space-y-0.5">
                {NAV_ITEMS.map(item => (
                    <SidebarNavItem
                        key={item.label}
                        item={item}
                        collapsed={collapsed}
                        currentUrl={url}
                    />
                ))}
            </nav>

            {/* ── User ── */}
            {user && (
                <div className={`border-t border-gray-100 p-3 flex-shrink-0 ${collapsed ? 'flex justify-center' : ''}`}>
                    {collapsed ? (
                        <div className="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center" title={user.name}>
                            <span className="text-blue-700 text-xs font-bold">{user.name?.charAt(0).toUpperCase()}</span>
                        </div>
                    ) : (
                        <div className="flex items-center gap-3 px-1 min-w-0">
                            <div className="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span className="text-blue-700 text-xs font-bold">{user.name?.charAt(0).toUpperCase()}</span>
                            </div>
                            <div className="min-w-0">
                                <p className="text-sm font-medium text-gray-900 truncate leading-tight">{user.name}</p>
                                <p className="text-[11px] text-gray-400 capitalize truncate mt-0.5">{role?.replace('_', ' ')}</p>
                            </div>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

// ─── Provider ─────────────────────────────────────────────────────────────────

export function SidebarProvider({ children }: { children: React.ReactNode }) {
    const [collapsed, setCollapsedState] = useState<boolean>(() => {
        try { return localStorage.getItem('sidebar_collapsed') === 'true'; }
        catch { return false; }
    });
    const [mobileOpen, setMobileOpen] = useState(false);

    const setCollapsed = (v: boolean) => {
        setCollapsedState(v);
        try { localStorage.setItem('sidebar_collapsed', String(v)); } catch {}
    };

    return (
        <SidebarContext.Provider value={{ collapsed, setCollapsed, mobileOpen, setMobileOpen }}>
            {children}
        </SidebarContext.Provider>
    );
}

// ─── AppSidebar — exported as BOTH named and default ─────────────────────────
// Named export  → import { AppSidebar }  (dipakai app-sidebar-layout.tsx)
// Default export → import AppSidebar     (dipakai app-layout.tsx)

export function AppSidebar() {
    const { collapsed, setCollapsed, mobileOpen, setMobileOpen } = useSidebarContext();

    return (
        <>
            {/* Desktop */}
            <aside className="hidden lg:flex flex-shrink-0 h-screen sticky top-0">
                <SidebarInner
                    collapsed={collapsed}
                    onToggle={() => setCollapsed(!collapsed)}
                />
            </aside>

            {/* Mobile overlay */}
            {mobileOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
                    onClick={() => setMobileOpen(false)}
                />
            )}

            {/* Mobile drawer */}
            <aside
                className={`
                    fixed inset-y-0 left-0 z-50 lg:hidden
                    transition-transform duration-300 ease-in-out
                    ${mobileOpen ? 'translate-x-0' : '-translate-x-full'}
                `}
            >
                <SidebarInner
                    collapsed={false}
                    onToggle={() => {}}
                    onClose={() => setMobileOpen(false)}
                    showClose
                />
            </aside>
        </>
    );
}

// ─── Mobile trigger ───────────────────────────────────────────────────────────

export function MobileMenuButton() {
    const { setMobileOpen } = useSidebarContext();
    return (
        <button
            onClick={() => setMobileOpen(true)}
            className="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors"
            aria-label="Buka menu"
        >
            <Menu className="w-5 h-5" />
        </button>
    );
}

export default AppSidebar;