import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ArrowLeft, Save } from 'lucide-react';
export default function DocumentsEdit({ document: doc, tim_kerja_list, users, status_options }: any) {
    const { data, setData, put, processing, errors } = useForm({
        judul:         doc.judul ?? '',
        nomor_dokumen: doc.nomor_dokumen ?? '',
        deadline:      doc.deadline ?? '',
        catatan:       doc.catatan ?? '',
        status:        doc.status ?? 'draft',
        assignee_id:   doc.assignee_id ?? '',
        tim_kerja_id:  doc.tim_kerja_id ?? '',
    });
    const submit = (e: React.FormEvent) => { e.preventDefault(); put(route('documents.update', doc.id)); };
    return (
        <AppLayout>
            <Head title={`Edit — ${doc.judul}`} />
            <form onSubmit={submit} className="flex flex-col gap-6 max-w-3xl">
                <div className="flex items-center gap-4">
                    <Link href={route('documents.index')} className="p-2 rounded-lg hover:bg-gray-100 text-gray-500"><ArrowLeft className="w-5 h-5" /></Link>
                    <h1 className="text-2xl font-semibold text-gray-900">Edit Dokumen</h1>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div className="sm:col-span-2">
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Judul *</label>
                        <input value={data.judul} onChange={e => setData('judul', e.target.value)} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        {errors.judul && <p className="mt-1 text-xs text-red-600">{errors.judul}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Nomor Dokumen</label>
                        <input value={data.nomor_dokumen} onChange={e => setData('nomor_dokumen', e.target.value)} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Status *</label>
                        <select value={data.status} onChange={e => setData('status', e.target.value)} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            {status_options?.map((s: any) => <option key={s.value} value={s.value}>{s.label}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Deadline</label>
                        <input type="date" value={data.deadline} onChange={e => setData('deadline', e.target.value)} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Assignee *</label>
                        <select value={data.assignee_id} onChange={e => setData('assignee_id', e.target.value)} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Pilih pengguna</option>
                            {users?.map((u: any) => <option key={u.id} value={u.id}>{u.name}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Tim Kerja</label>
                        <select value={data.tim_kerja_id} onChange={e => setData('tim_kerja_id', e.target.value)} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Pilih tim kerja</option>
                            {tim_kerja_list?.map((tk: any) => <option key={tk.id} value={tk.id}>[{tk.kode}] {tk.nama}</option>)}
                        </select>
                    </div>
                    <div className="sm:col-span-2">
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea value={data.catatan} onChange={e => setData('catatan', e.target.value)} rows={3} className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" />
                    </div>
                </div>
                <div className="flex justify-end gap-3 pb-6">
                    <Link href={route('documents.index')} className="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</Link>
                    <button type="submit" disabled={processing} className="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-medium rounded-lg">
                        <Save className="w-4 h-4" />{processing ? 'Menyimpan...' : 'Simpan'}
                    </button>
                </div>
            </form>
        </AppLayout>
    );
}
