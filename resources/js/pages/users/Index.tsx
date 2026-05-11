import React, { useState, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {
    Search, Plus, Edit2, Trash2, Filter, X,
    ChevronLeft, ChevronRight, MoreVertical,
    Users, Eye, ShieldCheck,
} from 'lucide-react';
import { debounce } from 'lodash';
import type { User, Paginated } from '@/types';

// ─── Role Config ──────────────────────────────────────────────────────────────

const ROLE_CONFIG: Record<string, { label: string; class: string }> = {
    admin:           { label: 'Administrator',    class: 'bg-red-100 text-red-700 border-red-200'       },
    kepala_tim_kerja:{ label: 'Kepala Tim Kerja', class: 'bg-purple-100 text-purple-700 border-purple-200' },
    staff:           { label: 'Staff',            class: 'bg-blue-100 text-blue-700 border-blue-200'    },
    viewer:          { label: 'Viewer',           class: 'bg-gray-100 text-gray-600 border-gray-200'    },
};

const STATUS_CONFIG: Record<string, { label: string; class: string }> = {
    active:   { label: 'Aktif',        class: 'bg-green-100 text-green-700 border-green-200' },
    inactive: { label: 'Tidak Aktif',  class: 'bg-gray-100 text-gray-500 border-gray-200'   },
};

function RoleBadge({ role }: { role: string }) {
    const cfg = ROLE_CONFIG[role] ?? { label: role, class: 'bg-gray-100 text-gray-600 border-gray-200' };
    return (
        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border ${cfg.class}`}>
            <ShieldCheck className="w-3 h-3" />
            {cfg.label}
        </span>
    );
}

function StatusBadge({ status }: { status: string }) {
    const cfg = STATUS_CONFIG[status] ?? STATUS_CONFIG.inactive;
    return (
        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border ${cfg.class}`}>
            <span className="w-1.5 h-1.5 rounded-full bg-current" />
            {cfg.label}
        </span>
    );
}

// ─── Action Menu ──────────────────────────────────────────────────────────────

function ActionMenu({ user }: { user: User }) {
    const [open, setOpen] = useState(false);

    const handleDelete = () => {
        if (confirm(`Hapus pengguna "${user.name}"?`)) {
            router.delete(route('users.destroy', user.id));
        }
        setOpen(false);
    };

    return (
        <div className="relative">
            <button
                onClick={() => setOpen(v => !v)}
                className="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors"
            >
                <MoreVertical className="w-4 h-4" />
            </button>
            {open && (
                <>
                    <div className="fixed inset-0 z-10" onClick={() => setOpen(false)} />
                    <div className="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-lg border border-gray-200 z-20 py-1 text-sm">
                        <Link
                            href={route('users.edit', user.id)}
                            className="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-50"
                            onClick={() => setOpen(false)}
                        >
                            <Edit2 className="w-4 h-4 text-gray-400" /> Edit
                        </Link>
                        <hr className="my-1 border-gray-100" />
                        <button
                            onClick={handleDelete}
                            className="flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 w-full text-left"
                        >
                            <Trash2 className="w-4 h-4" /> Hapus
                        </button>
                    </div>
                </>
            )}
        </div>
    );
}

// ─── Pagination ───────────────────────────────────────────────────────────────

function PaginationBtn({ href, active, disabled, children, label }: {
    href?: string | null; active?: boolean; disabled?: boolean;
    children?: React.ReactNode; label?: string;
}) {
    const base  = 'inline-flex items-center justify-center min-w-[34px] h-[34px] px-2 text-sm rounded-lg transition-colors font-medium';
    const style = active ? 'bg-blue-600 text-white'
        : disabled ? 'text-gray-300 cursor-not-allowed pointer-events-none'
        : 'text-gray-600 hover:bg-gray-100';

    if (!href || disabled) return <span className={`${base} ${style}`}>{children ?? label}</span>;
    return <Link href={href} className={`${base} ${style}`} preserveScroll preserveState>{children ?? label}</Link>;
}

// ─── Filters ─────────────────────────────────────────────────────────────────

interface Filters {
    search?: string;
    role?:   string;
    status?: string;
}

interface Props {
    users:   Paginated<User>;
    filters: Filters;
}

// ─── Main ─────────────────────────────────────────────────────────────────────

export default function UsersIndex({ users, filters }: Props) {
    const [localSearch, setLocalSearch] = useState(filters.search ?? '');
    const [showFilter, setShowFilter]   = useState(!!(filters.role || filters.status));

    const applySearch = useCallback(
        debounce((search: string) => {
            router.get(route('users.index'), { ...filters, search: search || undefined }, {
                preserveState: true, preserveScroll: true, replace: true,
            });
        }, 400),
        [filters]
    );

    const handleSearch = (v: string) => { setLocalSearch(v); applySearch(v); };

    const handleFilter = (key: keyof Filters, value: string) => {
        router.get(route('users.index'), { ...filters, [key]: value || undefined }, {
            preserveState: true, preserveScroll: true, replace: true,
        });
    };

    const clearFilters = () => {
        setLocalSearch('');
        router.get(route('users.index'), {}, { replace: true });
    };

    const activeFilterCount = [filters.role, filters.status].filter(Boolean).length;

    const formatDate = (d: string | null) => d
        ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
        : '—';

    return (
        <AppLayout>
            <Head title="Pengguna" />
            <div className="flex flex-col gap-6">

                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Pengguna</h1>
                        <p className="text-sm text-gray-500 mt-0.5">{users.total} pengguna terdaftar</p>
                    </div>
                    <Link
                        href={route('users.create')}
                        className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                    >
                        <Plus className="w-4 h-4" /> Tambah Pengguna
                    </Link>
                </div>

                {/* Search + Filter */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 p-4">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            <input
                                type="text"
                                value={localSearch}
                                onChange={e => handleSearch(e.target.value)}
                                placeholder="Cari nama, email, NIP..."
                                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <button
                            onClick={() => setShowFilter(v => !v)}
                            className={`inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border transition-colors flex-shrink-0 ${
                                showFilter || activeFilterCount > 0
                                    ? 'bg-blue-50 border-blue-200 text-blue-700'
                                    : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                            }`}
                        >
                            <Filter className="w-4 h-4" />
                            Filter
                            {activeFilterCount > 0 && (
                                <span className="bg-blue-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center leading-none">
                                    {activeFilterCount}
                                </span>
                            )}
                        </button>
                        {(localSearch || activeFilterCount > 0) && (
                            <button onClick={clearFilters} className="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-500 hover:text-red-600 transition-colors flex-shrink-0">
                                <X className="w-4 h-4" /> Reset
                            </button>
                        )}
                    </div>

                    {showFilter && (
                        <div className="border-t border-gray-100 px-4 pb-4 pt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label className="block text-xs font-medium text-gray-500 mb-1.5">Role</label>
                                <select
                                    value={filters.role ?? ''}
                                    onChange={e => handleFilter('role', e.target.value)}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                >
                                    <option value="">Semua Role</option>
                                    {Object.entries(ROLE_CONFIG).map(([v, cfg]) => (
                                        <option key={v} value={v}>{cfg.label}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                                <select
                                    value={filters.status ?? ''}
                                    onChange={e => handleFilter('status', e.target.value)}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    )}
                </div>

                {/* Table */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/70">
                                    {['Pengguna', 'NIP', 'Jabatan Fungsional', 'Role', 'Status', 'Login Terakhir', ''].map((h, i) => (
                                        <th key={i} className={`text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide ${
                                            i === 1 ? 'hidden md:table-cell' :
                                            i === 2 ? 'hidden lg:table-cell' :
                                            i === 5 ? 'hidden lg:table-cell' :
                                            i === 6 ? 'w-10' : ''
                                        }`}>
                                            {h}
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {users.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="px-4 py-16 text-center">
                                            <div className="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                                <Users className="w-6 h-6 text-gray-400" />
                                            </div>
                                            <p className="text-gray-500 font-medium text-sm">Tidak ada pengguna</p>
                                            {(localSearch || activeFilterCount > 0) && (
                                                <button onClick={clearFilters} className="mt-2 text-xs text-blue-600 hover:underline">
                                                    Reset filter
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ) : users.data.map(user => (
                                    <tr key={user.id} className="hover:bg-gray-50/60 transition-colors">
                                        {/* Pengguna */}
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                    {user.avatar
                                                        ? <img src={user.avatar_url} alt={user.name} className="w-8 h-8 rounded-full object-cover" />
                                                        : <span className="text-blue-700 text-xs font-bold">{user.name.charAt(0).toUpperCase()}</span>
                                                    }
                                                </div>
                                                <div className="min-w-0">
                                                    <p className="font-medium text-gray-900 truncate">{user.name}</p>
                                                    <p className="text-xs text-gray-400 truncate">{user.email}</p>
                                                </div>
                                            </div>
                                        </td>
                                        {/* NIP */}
                                        <td className="px-4 py-3 hidden md:table-cell">
                                            <span className="font-mono text-xs text-gray-600">{user.nip ?? '—'}</span>
                                        </td>
                                        {/* Jabfung */}
                                        <td className="px-4 py-3 hidden lg:table-cell">
                                            <span className="text-sm text-gray-600 line-clamp-1">{user.jabfung ?? '—'}</span>
                                        </td>
                                        {/* Role */}
                                        <td className="px-4 py-3">
                                            <RoleBadge role={user.role} />
                                        </td>
                                        {/* Status */}
                                        <td className="px-4 py-3">
                                            <StatusBadge status={user.status} />
                                        </td>
                                        {/* Last Login */}
                                        <td className="px-4 py-3 hidden lg:table-cell">
                                            <span className="text-sm text-gray-500">{formatDate(user.last_login_at)}</span>
                                        </td>
                                        {/* Actions */}
                                        <td className="px-4 py-3 text-right">
                                            <ActionMenu user={user} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {users.last_page > 1 && (
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t border-gray-100 bg-gray-50/40">
                            <p className="text-sm text-gray-500">
                                <span className="font-medium text-gray-700">{users.from}–{users.to}</span> dari{' '}
                                <span className="font-medium text-gray-700">{users.total}</span> pengguna
                            </p>
                            <div className="flex items-center gap-1 flex-wrap">
                                <PaginationBtn href={users.prev_page_url} disabled={!users.prev_page_url}>
                                    <ChevronLeft className="w-4 h-4" />
                                </PaginationBtn>
                                {users.links.slice(1, -1).map((link, i) => (
                                    <PaginationBtn key={i} href={link.url} active={link.active} disabled={!link.url} label={link.label} />
                                ))}
                                <PaginationBtn href={users.next_page_url} disabled={!users.next_page_url}>
                                    <ChevronRight className="w-4 h-4" />
                                </PaginationBtn>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}