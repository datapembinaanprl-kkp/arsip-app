import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ArrowLeft, Save } from 'lucide-react';
import type { TimKerja, User } from '@/types';

// ─── Shared Form Components ───────────────────────────────────────────────────

function Field({ label, error, required, hint, full, children }: {
    label: string; error?: string; required?: boolean; hint?: string; full?: boolean; children: React.ReactNode;
}) {
    return (
        <div className={full ? 'sm:col-span-2' : ''}>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">
                {label}{required && <span className="text-red-500 ml-0.5">*</span>}
            </label>
            {children}
            {hint && !error && <p className="mt-1 text-xs text-gray-400">{hint}</p>}
            {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
        </div>
    );
}

function Input({ error, ...props }: React.InputHTMLAttributes<HTMLInputElement> & { error?: string }) {
    return (
        <input
            {...props}
            className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ${
                error ? 'border-red-300 bg-red-50' : 'border-gray-200'
            }`}
        />
    );
}

function Select({ error, children, ...props }: React.SelectHTMLAttributes<HTMLSelectElement> & { error?: string }) {
    return (
        <select {...props} className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white ${error ? 'border-red-300 bg-red-50' : 'border-gray-200'}`}>
            {children}
        </select>
    );
}

function Textarea({ error, ...props }: React.TextareaHTMLAttributes<HTMLTextAreaElement> & { error?: string }) {
    return (
        <textarea
            {...props}
            rows={props.rows ?? 3}
            className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none ${
                error ? 'border-red-300 bg-red-50' : 'border-gray-200'
            }`}
        />
    );
}

// ─── Props ────────────────────────────────────────────────────────────────────

interface Props {
    tim_kerja_list: TimKerja[];
    users:          Pick<User, 'id' | 'name'>[];
    status_options: { value: string; label: string }[];
}

// ─── Main ─────────────────────────────────────────────────────────────────────

export default function DocumentsCreate({ tim_kerja_list, users, status_options }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        judul:         '',
        nomor_dokumen: '',
        deadline:      '',
        catatan:       '',
        status:        'draft',
        assignee_id:   '',
        tim_kerja_id:  '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('documents.store'));
    };

    return (
        <AppLayout>
            <Head title="Tambah Dokumen" />
            <form onSubmit={submit} className="flex flex-col gap-6 max-w-3xl">

                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link href={route('documents.index')} className="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <ArrowLeft className="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Tambah Dokumen</h1>
                        <p className="text-sm text-gray-500 mt-0.5">Buat entri dokumen baru</p>
                    </div>
                </div>

                {/* Form */}
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 className="text-sm font-semibold text-gray-700">Informasi Dokumen</h2>
                    </div>
                    <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <Field label="Judul Dokumen" error={errors.judul} required full>
                            <Input
                                value={data.judul}
                                onChange={e => setData('judul', e.target.value)}
                                placeholder="Masukkan judul dokumen"
                                error={errors.judul}
                            />
                        </Field>

                        <Field label="Nomor Dokumen" error={errors.nomor_dokumen} hint="Opsional, harus unik">
                            <Input
                                value={data.nomor_dokumen}
                                onChange={e => setData('nomor_dokumen', e.target.value)}
                                placeholder="Misal: DOC/2024/001"
                                error={errors.nomor_dokumen}
                            />
                        </Field>

                        <Field label="Status" error={errors.status} required>
                            <Select value={data.status} onChange={e => setData('status', e.target.value)} error={errors.status}>
                                {status_options.map(s => (
                                    <option key={s.value} value={s.value}>{s.label}</option>
                                ))}
                            </Select>
                        </Field>

                        <Field label="Deadline" error={errors.deadline}>
                            <Input
                                type="date"
                                value={data.deadline}
                                onChange={e => setData('deadline', e.target.value)}
                                error={errors.deadline}
                            />
                        </Field>

                        <Field label="Assignee" error={errors.assignee_id} required hint="Penanggung jawab dokumen">
                            <Select value={data.assignee_id} onChange={e => setData('assignee_id', e.target.value)} error={errors.assignee_id}>
                                <option value="">Pilih pengguna</option>
                                {users.map(u => (
                                    <option key={u.id} value={u.id}>{u.name}</option>
                                ))}
                            </Select>
                        </Field>

                        <Field label="Tim Kerja" error={errors.tim_kerja_id}>
                            <Select value={data.tim_kerja_id} onChange={e => setData('tim_kerja_id', e.target.value)} error={errors.tim_kerja_id}>
                                <option value="">Pilih tim kerja (opsional)</option>
                                {tim_kerja_list.map(tk => (
                                    <option key={tk.id} value={tk.id}>[{tk.kode}] {tk.nama}</option>
                                ))}
                            </Select>
                        </Field>

                        <Field label="Catatan" error={errors.catatan} full>
                            <Textarea
                                value={data.catatan}
                                onChange={e => setData('catatan', e.target.value)}
                                placeholder="Catatan tambahan mengenai dokumen ini..."
                                error={errors.catatan}
                            />
                        </Field>
                    </div>
                </div>

                {/* Actions */}
                <div className="flex items-center justify-end gap-3 pb-6">
                    <Link href={route('documents.index')} className="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </Link>
                    <button
                        type="submit"
                        disabled={processing}
                        className="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                    >
                        <Save className="w-4 h-4" />
                        {processing ? 'Menyimpan...' : 'Simpan Dokumen'}
                    </button>
                </div>
            </form>
        </AppLayout>
    );
}