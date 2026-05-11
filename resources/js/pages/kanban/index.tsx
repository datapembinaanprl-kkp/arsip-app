import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Kanban } from 'lucide-react';

const STATUS_LABELS: Record<string, string> = {
    'draft': 'Draft',
    'review': 'Menunggu Review',
    'approved': 'Disetujui',
    'revisi': 'Revisi',
    'rejected': 'Ditolak',
    'archived': 'Diarsipkan',
};

const STATUS_COLORS: Record<string, string> = {
    'draft': 'bg-gray-100 text-gray-800',
    'review': 'bg-yellow-100 text-yellow-800',
    'approved': 'bg-green-100 text-green-800',
    'revisi': 'bg-blue-100 text-blue-800',
    'rejected': 'bg-red-100 text-red-800',
    'archived': 'bg-gray-200 text-gray-600',
};

export default function KanbanIndex({ columns, summary }: { columns: any; summary: any }) {
    return (
        <AppLayout>
            <Head title="Kanban" />
            <div className="flex flex-col gap-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Kanban</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Tampilan alur dokumen</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div className="flex items-center gap-4 mb-6">
                        <Kanban className="w-5 h-5 text-gray-500" />
                        <p className="text-sm text-gray-600">Total dokumen: {summary.total}</p>
                    </div>
                    <div className="flex gap-4 overflow-x-auto">
                        {Object.keys(columns).map((status) => (
                            <div key={status} className="min-w-[250px] bg-gray-50 rounded-lg p-4 flex-shrink-0">
                                <h2 className={`text-sm font-medium mb-4 ${STATUS_COLORS[status]}`}>
                                    {STATUS_LABELS[status]} ({columns[status].length})
                                </h2>
                                <div className="flex flex-col gap-3">
                                    {columns[status].map((doc: any) => (
                                        <div key={doc.id} className="bg-white rounded-lg shadow p-3">
                                            <p className="font-medium text-gray-900">{doc.title}</p>
                                            <p className="text-xs text-gray-500 mt-0.5">Oleh {doc.assignee?.name ?? '—'}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}