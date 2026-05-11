import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {
    FileText, Users, TrendingUp, AlertCircle,
    Clock, CheckCircle2, XCircle, ArrowRight,
    AlertTriangle,
} from 'lucide-react';

// ─── Types ────────────────────────────────────────────────────────────────────

interface Stats {
    total_dokumen:    number;
    upload_bulan_ini: number;
    dokumen_ditolak:  number;
    dokumen_review:   number;
    dokumen_approved: number;
    staf_aktif:       number | null;
}

interface RecentDoc {
    id:            number;
    judul:         string;
    nomor_dokumen: string | null;
    status:        string;
    deadline:      string | null;
    created_at:    string;
    assignee?:     { id: number; name: string } | null;
    tim_kerja?:    { id: number; nama: string; kode: string } | null;
}

interface DeadlineAlert {
    id:       number;
    judul:    string;
    status:   string;
    deadline: string;
    assignee?: { id: number; name: string } | null;
}

interface RecentActivity {
    id:          number;
    description: string;
    created_at:  string;
    user?:       { id: number; name: string } | null;
}

interface Props {
    stats:          Stats;
    chartData:      number[];
    recentDocs:     RecentDoc[];
    recentActivity: RecentActivity[];
    progresStatus:  Record<string, number>;
    deadlineAlert:  DeadlineAlert[];
}

// ─── Status Config ────────────────────────────────────────────────────────────

const STATUS_CONFIG: Record<string, { label: string; class: string }> = {
    draft:    { label: 'Draft',     class: 'bg-gray-100 text-gray-600'       },
    review:   { label: 'Review',    class: 'bg-yellow-100 text-yellow-700'   },
    approved: { label: 'Disetujui', class: 'bg-green-100 text-green-700'     },
    rejected: { label: 'Ditolak',   class: 'bg-red-100 text-red-600'         },
    archived: { label: 'Diarsip',   class: 'bg-blue-100 text-blue-700'       },
};

function StatusBadge({ status }: { status: string }) {
    const cfg = STATUS_CONFIG[status] ?? { label: status, class: 'bg-gray-100 text-gray-600' };
    return (
        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cfg.class}`}>
            {cfg.label}
        </span>
    );
}

// ─── Stat Card ────────────────────────────────────────────────────────────────

function StatCard({
    label, value, icon: Icon, color, sub,
}: {
    label: string;
    value: number | string;
    icon: React.ComponentType<{ className?: string }>;
    color: string;
    sub?: string;
}) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-start gap-4">
            <div className={`w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ${color}`}>
                <Icon className="w-5 h-5" />
            </div>
            <div className="min-w-0">
                <p className="text-sm text-gray-500">{label}</p>
                <p className="text-2xl font-bold text-gray-900 mt-0.5">{value}</p>
                {sub && <p className="text-xs text-gray-400 mt-0.5">{sub}</p>}
            </div>
        </div>
    );
}

// ─── Mini Bar Chart ───────────────────────────────────────────────────────────

const BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

function MiniBarChart({ data }: { data: number[] }) {
    const max = Math.max(...data, 1);
    return (
        <div className="flex items-end gap-1 h-20">
            {data.map((val, i) => (
                <div key={i} className="flex-1 flex flex-col items-center gap-1 group">
                    <div className="relative w-full">
                        <div
                            className="w-full bg-blue-500 rounded-t transition-all group-hover:bg-blue-600"
                            style={{ height: `${Math.max((val / max) * 64, val > 0 ? 4 : 0)}px` }}
                        />
                        {val > 0 && (
                            <div className="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                                {val}
                            </div>
                        )}
                    </div>
                    <span className="text-[9px] text-gray-400">{BULAN[i]}</span>
                </div>
            ))}
        </div>
    );
}

// ─── Status Donut (simple bar-based) ─────────────────────────────────────────

function StatusProgress({ progresStatus }: { progresStatus: Record<string, number> }) {
    const total = Object.values(progresStatus).reduce((a, b) => a + b, 0);
    if (total === 0) return <p className="text-sm text-gray-400 py-4 text-center">Belum ada data</p>;

    const colors: Record<string, string> = {
        draft:    'bg-gray-400',
        review:   'bg-yellow-400',
        approved: 'bg-green-500',
        rejected: 'bg-red-400',
        archived: 'bg-blue-400',
    };

    return (
        <div className="space-y-3">
            {Object.entries(progresStatus).map(([status, count]) => {
                const pct = Math.round((count / total) * 100);
                const cfg = STATUS_CONFIG[status] ?? { label: status, class: '' };
                return (
                    <div key={status}>
                        <div className="flex items-center justify-between mb-1">
                            <span className="text-sm text-gray-600">{cfg.label}</span>
                            <span className="text-sm font-medium text-gray-900">{count} <span className="text-gray-400 font-normal">({pct}%)</span></span>
                        </div>
                        <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                className={`h-full rounded-full transition-all ${colors[status] ?? 'bg-gray-400'}`}
                                style={{ width: `${pct}%` }}
                            />
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

// ─── Format Date ─────────────────────────────────────────────────────────────

function fmtDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function deadlineDiff(d: string) {
    const diff = Math.ceil((new Date(d).getTime() - Date.now()) / 86_400_000);
    if (diff < 0)  return { label: `${Math.abs(diff)} hari lalu`, class: 'text-red-600' };
    if (diff === 0) return { label: 'Hari ini', class: 'text-orange-600 font-semibold' };
    if (diff <= 3)  return { label: `${diff} hari lagi`, class: 'text-orange-500' };
    return { label: `${diff} hari lagi`, class: 'text-gray-500' };
}

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function Dashboard({
    stats,
    chartData,
    recentDocs,
    recentActivity,
    progresStatus,
    deadlineAlert,
}: Props) {
    // Guard: jika props belum ada (misalnya route belum pakai controller)
    if (!stats) {
        return (
            <AppLayout>
                <Head title="Dashboard" />
                <div className="flex items-center justify-center h-64">
                    <p className="text-gray-400 text-sm">Memuat dashboard...</p>
                </div>
            </AppLayout>
        );
    }

    const currentMonth = new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });

    return (
        <AppLayout>
            <Head title="Dashboard" />

            <div className="flex flex-col gap-6">

                {/* ── Header ── */}
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Ringkasan sistem arsip — {currentMonth}</p>
                </div>

                {/* ── Stat Cards ── */}
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    <StatCard
                        label="Total Dokumen"
                        value={stats.total_dokumen}
                        icon={FileText}
                        color="bg-blue-100 text-blue-600"
                    />
                    <StatCard
                        label="Upload Bulan Ini"
                        value={stats.upload_bulan_ini}
                        icon={TrendingUp}
                        color="bg-green-100 text-green-600"
                        sub={currentMonth}
                    />
                    <StatCard
                        label="Menunggu Review"
                        value={stats.dokumen_review}
                        icon={Clock}
                        color="bg-yellow-100 text-yellow-600"
                    />
                    <StatCard
                        label="Disetujui"
                        value={stats.dokumen_approved}
                        icon={CheckCircle2}
                        color="bg-emerald-100 text-emerald-600"
                    />
                    <StatCard
                        label="Ditolak"
                        value={stats.dokumen_ditolak}
                        icon={XCircle}
                        color="bg-red-100 text-red-600"
                    />
                    {stats.staf_aktif !== null && (
                        <StatCard
                            label="Staf Aktif"
                            value={stats.staf_aktif}
                            icon={Users}
                            color="bg-purple-100 text-purple-600"
                        />
                    )}
                </div>

                {/* ── Main Grid ── */}
                <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    {/* Chart + Recent Docs (2/3) */}
                    <div className="xl:col-span-2 flex flex-col gap-6">

                        {/* Chart */}
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <div className="flex items-center justify-between mb-5">
                                <h2 className="text-sm font-semibold text-gray-700">Upload Dokumen per Bulan</h2>
                                <span className="text-xs text-gray-400">{new Date().getFullYear()}</span>
                            </div>
                            <MiniBarChart data={chartData ?? Array(12).fill(0)} />
                        </div>

                        {/* Recent Docs */}
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                                <h2 className="text-sm font-semibold text-gray-700">Dokumen Terbaru</h2>
                                <Link
                                    href={route('documents.index')}
                                    className="text-xs text-blue-600 hover:underline flex items-center gap-1"
                                >
                                    Lihat semua <ArrowRight className="w-3 h-3" />
                                </Link>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {recentDocs && recentDocs.length > 0 ? (
                                    recentDocs.map(doc => (
                                        <div key={doc.id} className="px-6 py-3 flex items-center gap-3 hover:bg-gray-50/50 transition-colors">
                                            <div className="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <FileText className="w-4 h-4 text-blue-400" />
                                            </div>
                                            <div className="min-w-0 flex-1">
                                                <Link
                                                    href={route('documents.show', doc.id)}
                                                    className="text-sm font-medium text-gray-900 hover:text-blue-600 truncate block"
                                                >
                                                    {doc.judul}
                                                </Link>
                                                <p className="text-xs text-gray-400 mt-0.5">
                                                    {doc.assignee?.name ?? '—'} · {fmtDate(doc.created_at)}
                                                </p>
                                            </div>
                                            <StatusBadge status={doc.status} />
                                        </div>
                                    ))
                                ) : (
                                    <div className="px-6 py-10 text-center">
                                        <FileText className="w-8 h-8 text-gray-200 mx-auto mb-2" />
                                        <p className="text-sm text-gray-400">Belum ada dokumen</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Right Column (1/3) */}
                    <div className="flex flex-col gap-6">

                        {/* Status Progress */}
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <h2 className="text-sm font-semibold text-gray-700 mb-4">Distribusi Status</h2>
                            <StatusProgress progresStatus={progresStatus ?? {}} />
                        </div>

                        {/* Deadline Alert */}
                        {deadlineAlert && deadlineAlert.length > 0 && (
                            <div className="bg-white rounded-xl border border-orange-200 shadow-sm overflow-hidden">
                                <div className="px-5 py-4 border-b border-orange-100 bg-orange-50/50 flex items-center gap-2">
                                    <AlertTriangle className="w-4 h-4 text-orange-500" />
                                    <h2 className="text-sm font-semibold text-orange-700">Deadline Mendekat</h2>
                                </div>
                                <div className="divide-y divide-gray-50">
                                    {deadlineAlert.map(doc => {
                                        const diff = deadlineDiff(doc.deadline);
                                        return (
                                            <div key={doc.id} className="px-5 py-3">
                                                <Link
                                                    href={route('documents.show', doc.id)}
                                                    className="text-sm font-medium text-gray-900 hover:text-blue-600 line-clamp-1"
                                                >
                                                    {doc.judul}
                                                </Link>
                                                <div className="flex items-center justify-between mt-1">
                                                    <span className="text-xs text-gray-400">{fmtDate(doc.deadline)}</span>
                                                    <span className={`text-xs font-medium ${diff.class}`}>{diff.label}</span>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}

                        {/* Recent Activity */}
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div className="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h2 className="text-sm font-semibold text-gray-700">Aktivitas Terbaru</h2>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {recentActivity && recentActivity.length > 0 ? (
                                    recentActivity.slice(0, 6).map(a => (
                                        <div key={a.id} className="px-5 py-3">
                                            <p className="text-sm text-gray-700 line-clamp-2">{a.description}</p>
                                            <div className="flex items-center justify-between mt-1">
                                                <span className="text-xs text-gray-400">{a.user?.name ?? 'Sistem'}</span>
                                                <span className="text-xs text-gray-300">{fmtDate(a.created_at)}</span>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="px-5 py-8 text-center">
                                        <p className="text-sm text-gray-400">Belum ada aktivitas</p>
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