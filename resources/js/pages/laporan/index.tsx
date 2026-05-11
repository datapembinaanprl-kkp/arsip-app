import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { FileDown } from 'lucide-react';

export default function LaporanIndex({ summary, chartBidang, chartTipe, aktivitasUser, surveyRecap, bulan, tahun }: { summary: any; chartBidang: any; chartTipe: any; aktivitasUser: any; surveyRecap: any; bulan: number; tahun: number }) {
    return (
        <AppLayout>
            <Head title="Laporan" />
            <div className="flex flex-col gap-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Laporan</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Rekapitulasi aktivitas dan arsip</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div className="flex items-center gap-4 mb-6">
                        <FileDown className="w-5 h-5 text-gray-500" />
                        <a href={route('laporan.export', { bulan, tahun })} className="text-sm text-blue-600 hover:underline">Export PDF</a>
                    </div>
                    {/* Summary cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div className="bg-gray-50 rounded-lg p-4">
                            <p className="text-sm text-gray-500">Total Arsip</p>
                            <p className="text-xl font-medium text-gray-900">{summary.total_arsip}</p>
                        </div>
                        <div className="bg-gray-50 rounded-lg p-4">
                            <p className="text-sm text-gray-500">Total Dokumen Masuk</p>
                            <p className="text-xl font-medium text-gray-900">{summary.total_masuk}</p>
                        </div>
                        <div className="bg-gray-50 rounded-lg p-4">
                            <p className="text-sm text-gray-500">Total Dokumen Selesai</p>
                            <p className="text-xl font-medium text-gray-900">{summary.total_selesai}</p>
                        </div>
                    </div>
                    {/* Charts and tables would go here */}
                    <p className="text-gray-600">[Grafik dan tabel rekapitulasi akan ditampilkan di sini]</p>
                </div>
            </div>
        </AppLayout>
    );
}