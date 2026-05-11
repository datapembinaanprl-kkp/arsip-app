import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ArrowLeft, Save } from 'lucide-react';

// ─── Shared Form Field ────────────────────────────────────────────────────────

function Field({ label, error, required, children }: {
    label: string; error?: string; required?: boolean; children: React.ReactNode;
}) {
    return (
        <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">
                {label}{required && <span className="text-red-500 ml-0.5">*</span>}
            </label>
            {children}
            {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
        </div>
    );
}

function Input({ error, ...props }: React.InputHTMLAttributes<HTMLInputElement> & { error?: string }) {
    return (
        <input
            {...props}
            className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors ${
                error ? 'border-red-300 bg-red-50' : 'border-gray-200'
            } ${props.disabled ? 'bg-gray-50 text-gray-500' : ''}`}
        />
    );
}

function Select({ error, children, ...props }: React.SelectHTMLAttributes<HTMLSelectElement> & { error?: string }) {
    return (
        <select
            {...props}
            className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white ${
                error ? 'border-red-300 bg-red-50' : 'border-gray-200'
            }`}
        >
            {children}
        </select>
    );
}

function SectionCard({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h2 className="text-sm font-semibold text-gray-700">{title}</h2>
            </div>
            <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                {children}
            </div>
        </div>
    );
}

// ─── Main ─────────────────────────────────────────────────────────────────────

export default function UsersCreate() {
    const { data, setData, post, processing, errors } = useForm({
    name:                '',
    email:               '',
    password:            '',
    password_confirmation: '',
    role:                'viewer',
    status:              'active',
    phone:               '',
    nip:                 '',
    pangkat_golongan:    '',
    jabatan_fungsional:  '', // ← bukan jabfung
    SPT:                 '', // ← uppercase
    SKP:                 '', // ← uppercase
});

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('users.store'));
    };

    return (
        <AppLayout>
            <Head title="Tambah Pengguna" />
            <form onSubmit={submit} className="flex flex-col gap-6 max-w-4xl">

                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link href={route('users.index')} className="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <ArrowLeft className="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Tambah Pengguna</h1>
                        <p className="text-sm text-gray-500 mt-0.5">Buat akun pengguna baru</p>
                    </div>
                </div>

                {/* Akun */}
                <SectionCard title="Informasi Akun">
                    <Field label="Nama Lengkap" error={errors.name} required>
                        <Input
                            value={data.name}
                            onChange={e => setData('name', e.target.value)}
                            placeholder="Masukkan nama lengkap"
                            error={errors.name}
                        />
                    </Field>
                    <Field label="Email" error={errors.email} required>
                        <Input
                            type="email"
                            value={data.email}
                            onChange={e => setData('email', e.target.value)}
                            placeholder="email@contoh.com"
                            error={errors.email}
                        />
                    </Field>
                    <Field label="Password" error={errors.password} required>
                        <Input
                            type="password"
                            value={data.password}
                            onChange={e => setData('password', e.target.value)}
                            placeholder="Minimal 8 karakter"
                            error={errors.password}
                        />
                    </Field>
                    <Field label="Konfirmasi Password" error={errors.password_confirmation} required>
                        <Input
                            type="password"
                            value={data.password_confirmation}
                            onChange={e => setData('password_confirmation', e.target.value)}
                            placeholder="Ulangi password"
                            error={errors.password_confirmation}
                        />
                    </Field>
                    <Field label="Role" error={errors.role} required>
                        <Select value={data.role} onChange={e => setData('role', e.target.value)} error={errors.role}>
                            <option value="viewer">Viewer</option>
                            <option value="staff">Staff</option>
                            <option value="kepala_tim_kerja">Kepala Tim Kerja</option>
                            <option value="admin">Administrator</option>
                        </Select>
                    </Field>
                    <Field label="Status" error={errors.status} required>
                        <Select value={data.status} onChange={e => setData('status', e.target.value)} error={errors.status}>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </Select>
                    </Field>
                </SectionCard>

                {/* Data Pegawai */}
                <SectionCard title="Data Kepegawaian">
                    <Field label="NIP" error={errors.nip}>
                        <Input value={data.nip} onChange={e => setData('nip', e.target.value)} placeholder="Nomor Induk Pegawai" error={errors.nip} />
                    </Field>
                    <Field label="No. Telepon" error={errors.phone}>
                        <Input value={data.phone} onChange={e => setData('phone', e.target.value)} placeholder="08xx-xxxx-xxxx" error={errors.phone} />
                    </Field>
                    <Field label="Pangkat / Golongan" error={errors.pangkat_golongan}>
                        <Input value={data.pangkat_golongan} onChange={e => setData('pangkat_golongan', e.target.value)} placeholder="Misal: Penata Muda / III-a" error={errors.pangkat_golongan} />
                    </Field>
                    <Field label="Jabatan Fungsional" error={errors.jabatan_fungsional}>
                        <Input value={data.jabatan_fungsional} onChange={e => setData('jabatan_fungsional', e.target.value)} placeholder="Jabatan fungsional" error={errors.jabatan_fungsional} />
                    </Field>
                    <Field label="SPT (Surat Perintah Tugas)" error={errors.SPT}>
                        <Input value={data.SPT} onChange={e => setData('SPT', e.target.value)} placeholder="Nomor SPT" error={errors.SPT} />
                    </Field>
                    <Field label="SKP (Sasaran Kinerja Pegawai)" error={errors.SKP}>
                        <Input value={data.SKP} onChange={e => setData('SKP', e.target.value)} placeholder="Nomor SKP" error={errors.SKP} />
                    </Field>
                </SectionCard>

                {/* Actions */}
                <div className="flex items-center justify-end gap-3 pb-6">
                    <Link href={route('users.index')} className="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </Link>
                    <button
                        type="submit"
                        disabled={processing}
                        className="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                    >
                        <Save className="w-4 h-4" />
                        {processing ? 'Menyimpan...' : 'Simpan Pengguna'}
                    </button>
                </div>
            </form>
        </AppLayout>
    );
}