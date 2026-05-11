import React, { useState, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {
    Search, Plus, Edit2, Trash2, MoreVertical,
    ChevronLeft, ChevronRight, FolderOpen, X,
} from 'lucide-react';
import { debounce } from 'lodash';
import type { Paginated, TimKerja } from '@/types';

// ─── Extended type with count ─────────────────────────────────────────────────

interface TimKerjaWithCount extends TimKerja {
    documents_count: number;
}

// ─── Action Menu ──────────────────────────────────────────────────────────────

function ActionMenu({ item }: { item: TimKerjaWithCount }) {
    const [open, setOpen] = useState(false);

    const handleDelete = () => {
        if (item.documents_count > 0) {
            alert(`Tim Kerja "${item.nama}" masih memiliki ${item.documents_count} dokumen dan tidak bisa dihapus.`);
            setOpen(false);
            return;
        }
        if (confirm(`Hapus Tim Kerja "${item.nama}"?`)) {
            router.delete(route('tim-kerja.destroy', item.id));
        }
        setOpen(false);
    };

    return (
        <div className="relative">
            <button onClick={() => setOpen(v => !v)} className="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors">
                <MoreVertical className="w-4 h-4" />
            </button>
            {open && (
                <>
                    <div className="fixed inset-0 z-10" onClick={() => setOpen(false)} />
                    <div className="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-20 py-1 text-sm">
                        <Link href={route('tim-kerja.edit', item.id)} className="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-50" onClick={() => setOpen(false)}>
                            <Edit2 className="w-4 h-4 text-gray-400" /> Edit
                        </Link>
                        <hr className="my-1 border-gray-100" />
                        <button onClick={handleDelete} className={`flex items-center gap-2 px-3 py-2 w-full text-left ${item.documents_count > 0 ? 'text-gray-300 cursor-not-allowed' : 'text-red-600 hover:bg-red-50'}`}>
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
    const style = active ? 'bg-blue-600 text-white' : disabled ? 'text-gray-300 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-100';
    if (!href || disabled) return <span className={`${base} ${style}`}>{children ?? label}</span>;
    return <Link href={href} className={`${base} ${style}`} preserveScroll preserveState>{children ?? label}</Link>;
}

// ─── Main ─────────────────────────────────────────────────────────────────────

interface Props {
    tim_kerjas: Paginated<TimKerjaWithCount>;
    filters:    { search?: string; status?: string };
}

export default function TimKerjaIndex({ tim_kerjas, filters }: Props) {
    const [localSearch, setLocalSearch] = useState(filters.search ?? '');

    const applySearch = useCallback(
        debounce((search: string) => {
            router.get(route('tim-kerja.index'), { ...filters, search: search || undefined }, {
                preserveState: true, preserveScroll: true, replace: true,
            });
        }, 400),
        [filters]
    );

    const handleSearch = (v: string) => { setLocalSearch(v); applySearch(v); };

    const handleStatusFilter = (value: string) => {
        router.get(route('tim-kerja.index'), { ...filters, status: value || undefined }, {
            preserveState: true, preserveScroll: true, replace: true,
        });
    };

    const clearFilters = () => {
        setLocalSearch('');
        router.get(route('tim-kerja.index'), {}, { replace: true });
    };

    const hasFilter = localSearch || filters.status;

    return (
        <AppLayout>
            <Head title="Tim Kerja" />
            <div className="flex flex-col gap-6">

                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Tim Kerja</h1>
                        <p className="text-sm text-gray-500 mt-0.5">{tim_kerjas.total} tim kerja terdaftar</p>
                    </div>
                    <Link href={route('tim-kerja.create')} className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <Plus className="w-4 h-4" /> Tambah Tim Kerja
                    </Link>
                </div>

                {/* Search */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 p-4">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            <input
                                type="text"
                                value={localSearch}
                                onChange={e => handleSearch(e.target.value)}
                                placeholder="Cari nama atau kode tim kerja..."
                                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <select
                            value={filters.status ?? ''}
                            onChange={e => handleStatusFilter(e.target.value)}
                            className="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white flex-shrink-0"
                        >
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                        {hasFilter && (
                            <button onClick={clearFilters} className="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-500 hover:text-red-600 transition-colors flex-shrink-0">
                                <X className="w-4 h-4" /> Reset
                            </button>
                        )}
                    </div>
                </div>

                {/* Table */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/70">
                                    <th className="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Tim Kerja</th>
                                    <th className="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide hidden sm:table-cell">Deskripsi</th>
                                    <th className="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Dokumen</th>
                                    <th className="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Status</th>
                                    <th className="w-10 px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {tim_kerjas.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-16 text-center">
                                            <div className="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                                <FolderOpen className="w-6 h-6 text-gray-400" />
                                            </div>
                                            <p className="text-gray-500 font-medium text-sm">Tidak ada tim kerja</p>
                                            {hasFilter && <button onClick={clearFilters} className="mt-2 text-xs text-blue-600 hover:underline">Reset filter</button>}
                                        </td>
                                    </tr>
                                ) : tim_kerjas.data.map(item => (
                                    <tr key={item.id} className="hover:bg-gray-50/60 transition-colors">
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <FolderOpen className="w-4 h-4 text-purple-600" />
                                                </div>
                                                <div>
                                                    <p className="font-medium text-gray-900">{item.nama}</p>
                                                    <p className="text-xs font-mono text-gray-400">{item.kode}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 hidden sm:table-cell">
                                            <p className="text-sm text-gray-600 line-clamp-2 max-w-[300px]">{item.deskripsi ?? '—'}</p>
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="inline-flex items-center gap-1 text-sm text-gray-600">
                                                <span className="font-medium text-gray-900">{item.documents_count}</span>
                                                <span className="text-gray-400">dokumen</span>
                                            </span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border ${
                                                item.is_active
                                                    ? 'bg-green-100 text-green-700 border-green-200'
                                                    : 'bg-gray-100 text-gray-500 border-gray-200'
                                            }`}>
                                                <span className="w-1.5 h-1.5 rounded-full bg-current" />
                                                {item.is_active ? 'Aktif' : 'Tidak Aktif'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <ActionMenu item={item} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {tim_kerjas.last_page > 1 && (
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t border-gray-100 bg-gray-50/40">
                            <p className="text-sm text-gray-500">
                                <span className="font-medium text-gray-700">{tim_kerjas.from}–{tim_kerjas.to}</span> dari{' '}
                                <span className="font-medium text-gray-700">{tim_kerjas.total}</span>
                            </p>
                            <div className="flex items-center gap-1">
                                <PaginationBtn href={tim_kerjas.prev_page_url} disabled={!tim_kerjas.prev_page_url}><ChevronLeft className="w-4 h-4" /></PaginationBtn>
                                {tim_kerjas.links.slice(1, -1).map((link, i) => <PaginationBtn key={i} href={link.url} active={link.active} disabled={!link.url} label={link.label} />)}
                                <PaginationBtn href={tim_kerjas.next_page_url} disabled={!tim_kerjas.next_page_url}><ChevronRight className="w-4 h-4" /></PaginationBtn>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}