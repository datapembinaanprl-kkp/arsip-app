import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Plus, Package } from 'lucide-react';

export default function AssetsIndex({ assets, filters }: { assets: any; filters: any }) {
    return (
        <AppLayout>
            <Head title="Aset" />
            <div className="flex flex-col gap-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Aset</h1>
                        <p className="text-sm text-gray-500 mt-0.5">{assets.total} aset</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <a href={route('assets.export.pdf')} className="px-3 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                            Export PDF
                        </a>
                        <Link href={route('assets.create')} className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <Plus className="w-4 h-4" /> Tambah Aset
                        </Link>
                    </div>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
                    {assets.data.length === 0 ? (
                        <div className="py-16 text-center">
                            <Package className="w-10 h-10 text-gray-300 mx-auto mb-3" />
                            <p className="text-gray-400 text-sm">Belum ada aset</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-gray-100 bg-gray-50/70">
                                        {['Nama Aset', 'Kode', 'Kondisi', 'Lokasi', ''].map((h, i) => (
                                            <th key={i} className="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {assets.data.map((a: any) => (
                                        <tr key={a.id} className="hover:bg-gray-50/60">
                                            <td className="px-4 py-3 font-medium text-gray-900">{a.nama}</td>
                                            <td className="px-4 py-3 font-mono text-xs text-gray-500">{a.kode ?? '—'}</td>
                                            <td className="px-4 py-3 text-gray-600">{a.kondisi ?? '—'}</td>
                                            <td className="px-4 py-3 text-gray-600">{a.lokasi ?? '—'}</td>
                                            <td className="px-4 py-3 text-right">
                                                <Link href={route('assets.show', a.id)} className="text-sm text-blue-600 hover:underline mr-3">Detail</Link>
                                                <Link href={route('assets.edit', a.id)} className="text-sm text-gray-600 hover:underline">Edit</Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}