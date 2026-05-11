import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {
    ArrowLeft, Edit2, Trash2, Clock, User,
    Users, FileText, Tag, History,
} from 'lucide-react';
import type { Document, DocumentHistory } from '@/types';

// ─── Status ───────────────────────────────────────────────────────────────────

type StatusKey = 'draft' | 'review' | 'approved' | 'rejected' | 'archived';

const STATUS_CONFIG: Record<StatusKey, { label: string; class: string }> = {
    draft:    { label: 'Draft',      class: 'bg-gray-100 text-gray-600 border-gray-200'       },
    review:   { label: 'Review',     class: 'bg-yellow-100 text-yellow-700 border-yellow-200' },
    approved: { label: 'Disetujui',  class: 'bg-green-100 text-green-700 border-green-200'    },
    rejected: { label: 'Ditolak',    class: 'bg-red-100 text-red-700 border-red-200'          },
    archived: { label: 'Diarsip',    class: 'bg-blue-100 text-blue-700 border-blue-200'       },
};

function StatusBadge({ status }: { status: string }) {
    const cfg = STATUS_CONFIG[status as StatusKey] ?? { label: status, class: 'bg-gray-100 text-gray-600 border-gray-200' };
    return (
        <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border ${cfg.class}`}>
            {cfg.label}
        </span>
    );
}

// ─── Info Row ─────────────────────────────────────────────────────────────────

function InfoRow({ icon: Icon, label, value }: {
    icon: React.ComponentType<{ className?: string }>;
    label: string;
    value: React.ReactNode;
}) {
    return (
        <div className="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
            <div className="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <Icon className="w-4 h-4 text-gray-500" />
            </div>
            <div className="min-w-0">
                <p className="text-xs text-gray-400 font-medium uppercase tracking-wide">{label}</p>
                <div className="text-sm text-gray-800 mt-0.5">{value}</div>
            </div>
        </div>
    );
}

// ─── History Item ─────────────────────────────────────────────────────────────

const FIELD_LABELS: Record<string, string> = {
    judul:         'Judul',
    nomor_dokumen: 'Nomor Dokumen',
    status:        'Status',
    deadline:      'Deadline',
    assignee_id:   'Assignee',
    tim_kerja_id:  'Tim Kerja',
    catatan:       'Catatan',
};

function HistoryItem({ history }: { history: DocumentHistory }) {
    const formatDate = (d: string) =>
        new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    return (
        <div className="flex gap-3 py-3 border-b border-gray-50 last:border-0">
            <div className="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <span className="text-blue-700 text-[10px] font-bold">
                    {history.changed_by_user?.name?.charAt(0).toUpperCase() ?? '?'}
                </span>
            </div>
            <div className="min-w-0 flex-1">
                <p className="text-sm text-gray-700">
                    <span className="font-medium">{history.changed_by_user?.name ?? 'Sistem'}</span>
                    {' '}mengubah{' '}
                    <span className="font-medium text-gray-900">{FIELD_LABELS[history.field] ?? history.field}</span>
                </p>
                <div className="flex items-center gap-2 mt-1 text-xs text-gray-400">
                    {history.old_value && (
                        <span className="line-through text-red-400">{history.old_value}</span>
                    )}
                    {history.old_value && history.new_value && <span>→</span>}
                    {history.new_value && (
                        <span className="text-green-600 font-medium">{history.new_value}</span>
                    )}
                </div>
                <p className="text-xs text-gray-400 mt-0.5">{formatDate(history.created_at)}</p>
            </div>
        </div>
    );
}

// ─── Props ────────────────────────────────────────────────────────────────────

interface Props {
    document: Document;
}

// ─── Main ─────────────────────────────────────────────────────────────────────

export default function DocumentsShow({ document: doc }: Props) {
    const formatDate = (d: string | null) => d
        ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
        : '—';

    const isOverdue = doc.deadline && new Date(doc.deadline) < new Date() && doc.status !== 'archived';

    const handleDelete = () => {
        if (confirm(`Hapus dokumen "${doc.judul}"?`)) {
            router.delete(route('documents.destroy', doc.id));
        }
    };

    return (
        <AppLayout>
            <Head title={doc.judul} />
            <div className="flex flex-col gap-6 max-w-5xl">

                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div className="flex items-start gap-4">
                        <Link href={route('documents.index')} className="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors mt-0.5">
                            <ArrowLeft className="w-5 h-5" />
                        </Link>
                        <div className="min-w-0">
                            <div className="flex items-center gap-3 flex-wrap">
                                <h1 className="text-2xl font-semibold text-gray-900">{doc.judul}</h1>
                                <StatusBadge status={doc.status} />
                            </div>
                            {doc.nomor_dokumen && (
                                <p className="text-sm text-gray-400 font-mono mt-1">#{doc.nomor_dokumen}</p>
                            )}
                        </div>
                    </div>
                    <div className="flex items-center gap-2 flex-shrink-0 pl-11 sm:pl-0">
                        <Link
                            href={route('documents.edit', doc.id)}
                            className="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <Edit2 className="w-4 h-4" /> Edit
                        </Link>
                        <button
                            onClick={handleDelete}
                            className="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                        >
                            <Trash2 className="w-4 h-4" /> Hapus
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {/* Detail */}
                    <div className="lg:col-span-2 flex flex-col gap-4">

                        {/* Overdue Alert */}
                        {isOverdue && (
                            <div className="bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2 text-red-700 text-sm">
                                <Clock className="w-4 h-4 flex-shrink-0" />
                                <span>Dokumen ini melewati deadline <strong>{formatDate(doc.deadline)}</strong></span>
                            </div>
                        )}

                        {/* Info Card */}
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h2 className="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                    <FileText className="w-4 h-4 text-gray-400" /> Detail Dokumen
                                </h2>
                            </div>
                            <div className="px-6 divide-y divide-gray-50">
                                <InfoRow icon={User} label="Assignee" value={
                                    doc.assignee
                                        ? <span className="inline-flex items-center gap-2">
                                            <span className="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-700">
                                                {doc.assignee.name.charAt(0).toUpperCase()}
                                            </span>
                                            {doc.assignee.name}
                                          </span>
                                        : '—'
                                } />
                                <InfoRow icon={Users} label="Tim Kerja" value={
                                    doc.tim_kerja
                                        ? <span className="flex items-center gap-1.5">
                                            <span className="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-mono">{doc.tim_kerja.kode}</span>
                                            {doc.tim_kerja.nama}
                                          </span>
                                        : '—'
                                } />
                                <InfoRow icon={Clock} label="Deadline" value={
                                    <span className={isOverdue ? 'text-red-600 font-medium' : ''}>
                                        {formatDate(doc.deadline)}
                                    </span>
                                } />
                                <InfoRow icon={User} label="Dibuat Oleh" value={doc.creator?.name ?? '—'} />
                                <InfoRow icon={Clock} label="Dibuat" value={formatDate(doc.created_at)} />
                                <InfoRow icon={Clock} label="Diperbarui" value={formatDate(doc.updated_at)} />
                            </div>
                        </div>

                        {/* Catatan */}
                        {doc.catatan && (
                            <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                                <div className="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                    <h2 className="text-sm font-semibold text-gray-700">Catatan</h2>
                                </div>
                                <div className="px-6 py-4">
                                    <p className="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{doc.catatan}</p>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* History */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-6">
                            <div className="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                                <History className="w-4 h-4 text-gray-400" />
                                <h2 className="text-sm font-semibold text-gray-700">Riwayat Perubahan</h2>
                            </div>
                            <div className="px-5 max-h-[500px] overflow-y-auto">
                                {doc.histories && doc.histories.length > 0 ? (
                                    doc.histories.map(h => <HistoryItem key={h.id} history={h} />)
                                ) : (
                                    <div className="py-10 text-center">
                                        <History className="w-8 h-8 text-gray-200 mx-auto mb-2" />
                                        <p className="text-xs text-gray-400">Belum ada riwayat perubahan</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}