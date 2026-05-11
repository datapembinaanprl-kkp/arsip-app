import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Plus, ClipboardList } from 'lucide-react';

export default function SurveyIndex({ surveys }: { surveys: any }) {
    return (
        <AppLayout>
            <Head title="Survey" />
            <div className="flex flex-col gap-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Survey</h1>
                        <p className="text-sm text-gray-500 mt-0.5">{surveys.total} survey</p>
                    </div>
                    <Link href={route('survey.create')} className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <Plus className="w-4 h-4" /> Buat Survey
                    </Link>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                    {surveys.data.length === 0 ? (
                        <div className="py-16 text-center">
                            <ClipboardList className="w-10 h-10 text-gray-300 mx-auto mb-3" />
                            <p className="text-gray-400 text-sm">Belum ada survey</p>
                        </div>
                    ) : (
                        <div className="divide-y divide-gray-50">
                            {surveys.data.map((s: any) => (
                                <div key={s.id} className="px-6 py-4 flex items-center justify-between">
                                    <div>
                                        <p className="font-medium text-gray-900">{s.title ?? s.judul}</p>
                                        <p className="text-xs text-gray-400 mt-0.5">{s.submissions_count} respon</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Link href={route('survey.show', s.id)} className="text-sm text-blue-600 hover:underline">Detail</Link>
                                        <Link href={route('survey.edit', s.id)} className="text-sm text-gray-600 hover:underline">Edit</Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}