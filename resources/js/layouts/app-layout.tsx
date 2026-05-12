import React from 'react';
import { AppSidebar, SidebarProvider, MobileMenuButton } from '@/components/app-sidebar';
import { usePage, Link } from '@inertiajs/react';
import { Bell, ChevronDown } from 'lucide-react';
import { SharedProps } from 'react';

interface Props {
    children: React.ReactNode;
    header?:  React.ReactNode;
}

export default function AppLayout({ children, header }: Props) {
    const { props } = usePage();
    const auth  = (props as any).auth;
    const flash = (props as any).flash as { success?: string; error?: string } | undefined;

    return (
        <SidebarProvider>
            <div className="flex h-screen bg-gray-50 overflow-hidden">

                <AppSidebar />

                <div className="flex-1 flex flex-col min-w-0 overflow-hidden">

                    {/* ── Top bar ── */}
                    <header className="flex-shrink-0 bg-white border-b border-gray-200 px-4 lg:px-6 h-14 flex items-center justify-between gap-4 z-30">
                        <div className="flex items-center gap-3">
                            <MobileMenuButton />
                            {header && <div className="text-sm text-gray-600">{header}</div>}
                        </div>

                        <div className="flex items-center gap-2">
                            <button className="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                                <Bell className="w-5 h-5" />
                                <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white" />
                            </button>

                            <div className="relative group">
                                <button className="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div className="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span className="text-white text-xs font-bold">
                                            {auth?.user?.name?.charAt(0).toUpperCase()}
                                        </span>
                                    </div>
                                    <span className="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">
                                        {auth?.user?.name}
                                    </span>
                                    <ChevronDown className="w-4 h-4 text-gray-400 hidden sm:block" />
                                </button>

                                <div className="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-150 z-50">
                                    <Link href="/profile" className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        Profil Saya
                                    </Link>
                                    <hr className="my-1 border-gray-100" />
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                    >
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </header>

                    {/* ── Flash ── */}
                    {(flash?.success || flash?.error) && (
                        <div className="px-4 lg:px-6 pt-4 flex-shrink-0">
                            {flash.success && (
                                <div className="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                                    <span className="w-2 h-2 bg-green-500 rounded-full flex-shrink-0" />
                                    {flash.success}
                                </div>
                            )}
                            {flash.error && (
                                <div className="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                                    <span className="w-2 h-2 bg-red-500 rounded-full flex-shrink-0" />
                                    {flash.error}
                                </div>
                            )}
                        </div>
                    )}

                    {/* ── Content ── */}
                    <main className="flex-1 overflow-y-auto px-4 lg:px-6 py-6">
                        {children}
                    </main>
                </div>
            </div>
        </SidebarProvider>
    );
}