import React, { useState, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {
    Search, Plus, Eye, Edit2, Trash2, FileText,
    ChevronLeft, ChevronRight, Filter, MoreVertical,
    X, Clock, CheckCircle2, XCircle, Archive, AlertCircle,
} from 'lucide-react';
import { debounce } from 'lodash';
import type { Document, Paginated } from '@/types';

// ─── Local Types ──────────────────────────────────────────────────────────────

interface TimKerja {
    id:   number;
    nama: string;
    kode: string;
}

interface Filters {
    search?:      string;
    tim_kerja_id?: string;
    status?:      string;
    deadline_from?: string;
    deadline_to?:   string;
}

interface Props {
    documents: Paginated<Document>;
    tim_kerja_list: TimKerja[];
    filters:   Filters;
}

// ─── Status Config ────────────────────────────────────────────────────────────

type StatusKey = 'draft' | 'review' | 'approved' | 'rejected' | 'archived';

const STATUS_CONFIG: Record<StatusKey, { label: string; class: string; icon: React.ComponentType<{ className?: string }> }> = {
    draft:    { label: 'Draft',      class: 'bg-gray-100 text-gray-600 border-gray-200',    icon: FileText      },
    review:   { label: 'Review',     class: 'bg-yellow-100 text-yellow-700 border-yellow-200', icon: AlertCircle },
    approved: { label: 'Disetujui',  class: 'bg-green-100 text-green-700 border-green-200',  icon: CheckCircle2  },
    rejected: { label: 'Ditolak',    class: 'bg-red-100 text-red-700 border-red-200',         icon: XCircle       },
    archived: { label: 'Diarsip',    class: 'bg-blue-100 text-blue-700 border-blue-200',     icon: Archive       },
};

const STATUS_OPTIONS = Object.entries(STATUS_CONFIG).map(([value, cfg]) => ({
    value,
    label: cfg.label,
}));

function StatusBadge({ status }: { status: string }) {
    const cfg = STATUS_CONFIG[status as StatusKey];
    if (!cfg) return <span className="text-gray-400 text-xs">{status}</span>;
    const Icon = cfg.icon;
    return (
        <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border ${cfg.class}`}>
            <Icon className="w-3 h-3" />
            {cfg.label}
        </span>
    );
}

// ─── Deadline Badge ───────────────────────────────────────────────────────────

function DeadlineBadge({ deadline }: { deadline: string | null }) {
    if (!deadline) return <span className="text-gray-300 text-sm">—</span>;

    const date     = new Date(deadline);
    const today    = new Date();
    const diffDays = Math.ceil((date.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    const formatted = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

    let colorClass = 'text-gray-600';
    if (diffDays < 0)  colorClass = 'text-red-600 font-medium';
    else if (diffDays <= 3) colorClass = 'text-orange-500 font-medium';
    else if (diffDays <= 7) colorClass = 'text-yellow-600';

    return (
        <span className={`inline-flex items-center gap-1 text-sm ${colorClass}`}>
            <Clock className="w-3.5 h-3.5" />
            {formatted}
        </span>
    );
}

// ─── Action Dropdown ──────────────────────────────────────────────────────────

function ActionMenu({ document: doc }: { document: Document }) {
    const [open, setOpen] = useState(false);

    const handleDelete = () => {
        if (confirm(`Hapus dokumen "${doc.judul}"?`)) {
             
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
                            href={route('documents.show', doc.id)}
                            className="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-50"
                            onClick={() => setOpen(false)}
                        >
                            <Eye className="w-4 h-4 text-gray-400" /> Detail
                        </Link>
                        <Link
                            href={route('documents.edit', doc.id)}
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

function PaginationBtn({
    href, active, disabled, children, label,
}: {
    href?: string | null;
    active?: boolean;
    disabled?: boolean;
    children?: React.ReactNode;
    label?: string;
}) {
    const base  = 'inline-flex items-center justify-center min-w-[34px] h-[34px] px-2 text-sm rounded-lg transition-colors font-medium';
    const style = active
        ? 'bg-blue-600 text-white'
        : disabled
            ? 'text-gray-300 cursor-not-allowed pointer-events-none'
            : 'text-gray-600 hover:bg-gray-100';

    if (!href || disabled) return <span className={`${base} ${style}`}>{children ?? label}</span>;

    return (
        <Link href={href} className={`${base} ${style}`} preserveScroll preserveState>
            {children ?? label}
        </Link>
    );
}

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function DocumentsIndex({ documents, tim_kerja_list, filters }: Props) {
    const [localSearch, setLocalSearch] = useState(filters.search ?? '');
    const [showFilter, setShowFilter]   = useState(
        !!(filters.tim_kerja_id || filters.status || filters.deadline_from || filters.deadline_to)
    );

    // Debounced search
    const applySearch = useCallback(
        debounce((search: string) => {
            router.get(route('documents.index'), { ...filters, search: search || undefined }, {
                preserveState:  true,
                preserveScroll: true,
                replace:        true,
            });
        }, 400),
        [filters]
    );

    const handleSearch = (value: string) => {
        setLocalSearch(value);
        applySearch(value);
    };

    const handleFilter = (key: keyof Filters, value: string) => {
        router.get(route('documents.index'), { ...filters, [key]: value || undefined }, {
            preserveState:  true,
            preserveScroll: true,
            replace:        true,
        });
    };

    const clearFilters = () => {
        setLocalSearch('');
        router.get(route('documents.index'), {}, { replace: true });
    };

    const activeFilterCount = [filters.tim_kerja_id, filters.status, filters.deadline_from, filters.deadline_to]
        .filter(Boolean).length;

    const formatDate = (d: string) =>
        new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

    return (
        <AppLayout>
            <Head title="Dokumen" />

            <div className="flex flex-col gap-6">

                {/* ── Header ── */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Dokumen</h1>
                        <p className="text-sm text-gray-500 mt-0.5">
                            {documents.total} dokumen ditemukan
                        </p>
                    </div>
                    <Link
                        href={route('documents.create')}
                        className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                    >
                        <Plus className="w-4 h-4" />
                        Tambah Dokumen
                    </Link>
                </div>

                {/* ── Search + Filter ── */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 p-4">
                        {/* Search */}
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            <input
                                type="text"
                                value={localSearch}
                                onChange={e => handleSearch(e.target.value)}
                                placeholder="Cari judul, nomor dokumen..."
                                className="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>

                        {/* Filter toggle */}
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
                            <button
                                onClick={clearFilters}
                                className="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-500 hover:text-red-600 transition-colors flex-shrink-0"
                            >
                                <X className="w-4 h-4" />
                                Reset
                            </button>
                        )}
                    </div>

                    {/* Filter panel */}
                    {showFilter && (
                        <div className="border-t border-gray-100 px-4 pb-4 pt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label className="block text-xs font-medium text-gray-500 mb-1.5">Tim Kerja</label>
                                <select
                                    value={filters.tim_kerja_id ?? ''}
                                    onChange={e => handleFilter('tim_kerja_id', e.target.value)}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                >
                                    <option value="">Semua Tim</option>
                                    {tim_kerja_list.map(tk => (
                                        <option key={tk.id} value={tk.id}>{tk.nama} ({tk.kode})</option>
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
                                    {STATUS_OPTIONS.map(s => (
                                        <option key={s.value} value={s.value}>{s.label}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-medium text-gray-500 mb-1.5">Deadline Dari</label>
                                <input
                                    type="date"
                                    value={filters.deadline_from ?? ''}
                                    onChange={e => handleFilter('deadline_from', e.target.value)}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-medium text-gray-500 mb-1.5">Deadline Sampai</label>
                                <input
                                    type="date"
                                    value={filters.deadline_to ?? ''}
                                    onChange={e => handleFilter('deadline_to', e.target.value)}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                    )}
                </div>

                {/* ── Table ── */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-100 bg-gray-50/70">
                                    {[
                                        { label: 'Judul Dokumen',                       class: '' },
                                        { label: 'Tim Kerja',                           class: 'hidden md:table-cell' },
                                        { label: 'Assignee',                            class: 'hidden lg:table-cell' },
                                        { label: 'Status',                              class: '' },
                                        { label: 'Deadline',                            class: 'hidden lg:table-cell' },
                                        { label: '',                                    class: 'w-10' },
                                    ].map((col, i) => (
                                        <th key={i} className={`text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide ${col.class}`}>
                                            {col.label}
                                        </th>
                                    ))}
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-gray-50">
                                {documents.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-4 py-16 text-center">
                                            <div className="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                                <FileText className="w-6 h-6 text-gray-400" />
                                            </div>
                                            <p className="text-gray-500 font-medium text-sm">Tidak ada dokumen</p>
                                            <p className="text-gray-400 text-xs mt-1">
                                                {localSearch || activeFilterCount > 0
                                                    ? 'Coba ubah filter pencarian'
                                                    : 'Belum ada dokumen yang ditambahkan'}
                                            </p>
                                            {(localSearch || activeFilterCount > 0) && (
                                                <button
                                                    onClick={clearFilters}
                                                    className="mt-3 text-xs text-blue-600 hover:underline"
                                                >
                                                    Reset semua filter
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ) : (
                                    documents.data.map(doc => (
                                        <tr key={doc.id} className="hover:bg-gray-50/60 transition-colors">
                                            {/* Judul */}
                                            <td className="px-4 py-3">
                                                <Link
                                                    href={route('documents.show', doc.id)}
                                                    className="font-medium text-gray-900 hover:text-blue-600 transition-colors line-clamp-1"
                                                >
                                                    {doc.judul}
                                                </Link>
                                                {doc.nomor_dokumen && (
                                                    <p className="text-xs text-gray-400 mt-0.5 font-mono">
                                                        #{doc.nomor_dokumen}
                                                    </p>
                                                )}
                                                {doc.catatan && (
                                                    <p className="text-xs text-gray-400 mt-0.5 line-clamp-1">
                                                        {doc.catatan}
                                                    </p>
                                                )}
                                            </td>

                                            {/* Tim Kerja */}
                                            <td className="px-4 py-3 hidden md:table-cell">
                                                {doc.tim_kerja ? (
                                                    <span className="inline-flex items-center gap-1.5 text-gray-600 text-sm">
                                                        <span className="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">
                                                            {doc.tim_kerja.kode}
                                                        </span>
                                                        {doc.tim_kerja.nama}
                                                    </span>
                                                ) : (
                                                    <span className="text-gray-300">—</span>
                                                )}
                                            </td>

                                            {/* Assignee */}
                                            <td className="px-4 py-3 hidden lg:table-cell">
                                                {doc.assignee ? (
                                                    <div className="flex items-center gap-2">
                                                        <div className="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                            <span className="text-blue-700 text-[10px] font-bold">
                                                                {doc.assignee.name.charAt(0).toUpperCase()}
                                                            </span>
                                                        </div>
                                                        <span className="text-gray-600 text-sm truncate max-w-[120px]">
                                                            {doc.assignee.name}
                                                        </span>
                                                    </div>
                                                ) : (
                                                    <span className="text-gray-300">—</span>
                                                )}
                                            </td>

                                            {/* Status */}
                                            <td className="px-4 py-3">
                                                <StatusBadge status={doc.status} />
                                            </td>

                                            {/* Deadline */}
                                            <td className="px-4 py-3 hidden lg:table-cell">
                                                <DeadlineBadge deadline={doc.deadline} />
                                            </td>

                                            {/* Actions */}
                                            <td className="px-4 py-3 text-right">
                                                <ActionMenu document={doc} />
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* ── Pagination ── */}
                    {documents.last_page > 1 && (
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t border-gray-100 bg-gray-50/40">
                            <p className="text-sm text-gray-500">
                                <span className="font-medium text-gray-700">{documents.from}–{documents.to}</span>
                                {' '}dari{' '}
                                <span className="font-medium text-gray-700">{documents.total}</span> dokumen
                            </p>
                            <div className="flex items-center gap-1 flex-wrap">
                                <PaginationBtn href={documents.prev_page_url} disabled={!documents.prev_page_url}>
                                    <ChevronLeft className="w-4 h-4" />
                                </PaginationBtn>
                                {documents.links.slice(1, -1).map((link, i) => (
                                    <PaginationBtn key={i} href={link.url} active={link.active} disabled={!link.url} label={link.label} />
                                ))}
                                <PaginationBtn href={documents.next_page_url} disabled={!documents.next_page_url}>
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