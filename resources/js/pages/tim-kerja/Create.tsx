import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ArrowLeft, Save } from 'lucide-react';

export default function TimKerjaCreate() {
    const { data, setData, post, processing, errors } = useForm({
        nama:      '',
        kode:      '',
        deskripsi: '',
        is_active: true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('tim-kerja.store'));
    };

    // Auto-uppercase kode
    const handleKode = (v: string) => setData('kode', v.toUpperCase().replace(/[^A-Z0-9\-]/g, ''));

    return (
        <AppLayout>
            <Head title="Tambah Tim Kerja" />
            <form onSubmit={submit} className="flex flex-col gap-6 max-w-xl">

                <div className="flex items-center gap-4">
                    <Link href={route('tim-kerja.index')} className="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <ArrowLeft className="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Tambah Tim Kerja</h1>
                        <p className="text-sm text-gray-500 mt-0.5">Buat unit tim kerja baru</p>
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 className="text-sm font-semibold text-gray-700">Informasi Tim Kerja</h2>
                    </div>
                    <div className="p-6 flex flex-col gap-5">

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Tim Kerja <span className="text-red-500">*</span>
                            </label>
                            <input
                                value={data.nama}
                                onChange={e => setData('nama', e.target.value)}
                                placeholder="Nama tim kerja"
                                className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 ${errors.nama ? 'border-red-300 bg-red-50' : 'border-gray-200'}`}
                            />
                            {errors.nama && <p className="mt-1 text-xs text-red-600">{errors.nama}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                Kode <span className="text-red-500">*</span>
                            </label>
                            <input
                                value={data.kode}
                                onChange={e => handleKode(e.target.value)}
                                placeholder="Misal: TIM-01"
                                className={`w-full px-3 py-2 text-sm border rounded-lg font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 ${errors.kode ? 'border-red-300 bg-red-50' : 'border-gray-200'}`}
                            />
                            <p className="mt-1 text-xs text-gray-400">Hanya huruf kapital, angka, dan tanda hubung. Contoh: TIM-01, ARSIP-A</p>
                            {errors.kode && <p className="mt-1 text-xs text-red-600">{errors.kode}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                            <textarea
                                value={data.deskripsi}
                                onChange={e => setData('deskripsi', e.target.value)}
                                rows={3}
                                placeholder="Deskripsi singkat tim kerja ini..."
                                className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none ${errors.deskripsi ? 'border-red-300 bg-red-50' : 'border-gray-200'}`}
                            />
                            {errors.deskripsi && <p className="mt-1 text-xs text-red-600">{errors.deskripsi}</p>}
                        </div>

                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                role="switch"
                                aria-checked={data.is_active}
                                onClick={() => setData('is_active', !data.is_active)}
                                className={`relative inline-flex w-10 h-5 rounded-full transition-colors flex-shrink-0 ${data.is_active ? 'bg-blue-600' : 'bg-gray-200'}`}
                            >
                                <span className={`inline-block w-4 h-4 bg-white rounded-full shadow transition-transform mt-0.5 ${data.is_active ? 'translate-x-5' : 'translate-x-0.5'}`} />
                            </button>
                            <div>
                                <p className="text-sm font-medium text-gray-700">Status Aktif</p>
                                <p className="text-xs text-gray-400">{data.is_active ? 'Tim kerja ini akan aktif dan bisa dipilih' : 'Tim kerja ini tidak aktif'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex items-center justify-end gap-3 pb-6">
                    <Link href={route('tim-kerja.index')} className="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </Link>
                    <button
                        type="submit"
                        disabled={processing}
                        className="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                    >
                        <Save className="w-4 h-4" />
                        {processing ? 'Menyimpan...' : 'Simpan'}
                    </button>
                </div>
            </form>
        </AppLayout>
    );
} 