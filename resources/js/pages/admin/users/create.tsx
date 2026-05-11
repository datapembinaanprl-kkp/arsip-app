import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

interface Role {
    id: number;
    name: string;
}

interface Props {
    roles: Role[];
}

export default function CreateUser({ roles }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        post(route('admin.users.store'));
    };

    const getRoleDescription = (role: string) => {
        switch (role) {
            case 'admin':
                return 'Akses penuh';
            case 'staf':
                return 'Upload & lihat dokumen sendiri';
            case 'supervisor':
                return '+ Dapat menolak dokumen';
            case 'direktur':
                return '+ Laporan & hapus dokumen';
            default:
                return role;
        }
    };

    return (
        <>
            <Head title="Tambah Pengguna" />

            <div className="space-y-6">
                <div>
                    <Link
                        href={route('admin.users')}
                        className="text-sm text-muted-foreground hover:underline"
                    >
                        ← Kembali
                    </Link>
                </div>

                <div>
                    <h1 className="text-2xl font-bold">
                        Tambah Pengguna
                    </h1>

                    <p className="text-muted-foreground">
                        Buat akun baru dan tentukan role-nya
                    </p>
                </div>

                <div className="max-w-2xl rounded-xl border bg-white p-6 shadow-sm">
                    <form onSubmit={submit} className="space-y-5">

                        {/* Nama */}
                        <div>
                            <label className="mb-2 block text-sm font-semibold">
                                Nama Lengkap
                                <span className="text-red-600"> *</span>
                            </label>

                            <input
                                type="text"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                placeholder="Contoh: Budi Santoso"
                                className={`w-full rounded-lg border px-4 py-2 text-sm outline-none transition ${
                                    errors.name
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                }`}
                            />

                            {errors.name && (
                                <div className="mt-1 text-xs text-red-600">
                                    {errors.name}
                                </div>
                            )}
                        </div>

                        {/* Email */}
                        <div>
                            <label className="mb-2 block text-sm font-semibold">
                                Email
                                <span className="text-red-600"> *</span>
                            </label>

                            <input
                                type="email"
                                value={data.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                                placeholder="contoh@arsip.id"
                                className={`w-full rounded-lg border px-4 py-2 text-sm outline-none transition ${
                                    errors.email
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                }`}
                            />

                            {errors.email && (
                                <div className="mt-1 text-xs text-red-600">
                                    {errors.email}
                                </div>
                            )}
                        </div>

                        {/* Password */}
                        <div>
                            <label className="mb-2 block text-sm font-semibold">
                                Password
                                <span className="text-red-600"> *</span>
                            </label>

                            <input
                                type="password"
                                value={data.password}
                                onChange={(e) =>
                                    setData('password', e.target.value)
                                }
                                placeholder="Minimal 8 karakter"
                                className={`w-full rounded-lg border px-4 py-2 text-sm outline-none transition ${
                                    errors.password
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                }`}
                            />

                            {errors.password && (
                                <div className="mt-1 text-xs text-red-600">
                                    {errors.password}
                                </div>
                            )}
                        </div>

                        {/* Confirm Password */}
                        <div>
                            <label className="mb-2 block text-sm font-semibold">
                                Konfirmasi Password
                                <span className="text-red-600"> *</span>
                            </label>

                            <input
                                type="password"
                                value={data.password_confirmation}
                                onChange={(e) =>
                                    setData(
                                        'password_confirmation',
                                        e.target.value
                                    )
                                }
                                placeholder="Ulangi password"
                                className="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm outline-none transition"
                            />
                        </div>

                        {/* Role */}
                        <div>
                            <label className="mb-2 block text-sm font-semibold">
                                Role
                                <span className="text-red-600"> *</span>
                            </label>

                            <select
                                value={data.role}
                                onChange={(e) =>
                                    setData('role', e.target.value)
                                }
                                className={`w-full rounded-lg border bg-white px-4 py-2 text-sm outline-none transition ${
                                    errors.role
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                }`}
                            >
                                <option value="">
                                    — Pilih Role —
                                </option>

                                {roles.map((role) => (
                                    <option
                                        key={role.id}
                                        value={role.name}
                                    >
                                        {role.name.charAt(0).toUpperCase() +
                                            role.name.slice(1)}{' '}
                                        —{' '}
                                        {getRoleDescription(role.name)}
                                    </option>
                                ))}
                            </select>

                            {errors.role && (
                                <div className="mt-1 text-xs text-red-600">
                                    {errors.role}
                                </div>
                            )}

                            <div className="mt-3 rounded-lg bg-muted p-4 text-xs leading-7 text-muted-foreground">
                                <strong>Keterangan role:</strong>
                                <br />
                                🔴 <strong>Admin</strong> — Kelola pengguna &
                                semua dokumen
                                <br />
                                🟣 <strong>Direktur</strong> — Lihat laporan,
                                hapus dokumen
                                <br />
                                🔵 <strong>Supervisor</strong> — Dapat menolak
                                dokumen staf
                                <br />
                                🟢 <strong>Staf</strong> — Upload & lihat
                                dokumen sendiri
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex gap-3">
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing
                                    ? 'Menyimpan...'
                                    : 'Tambah Pengguna'}
                            </button>

                            <Link
                                href={route('admin.users')}
                                className="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}

CreateUser.layout = (page: React.ReactNode) => (
    <AppLayout>{page}</AppLayout>
);