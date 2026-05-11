import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

export default function About() {
    return (
        <AppLayout>
            <Head title="Tentang" />
            <div className="flex flex-col gap-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Tentang Web Arsip</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Informasi sistem</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <p className="text-gray-600">Sistem Manajemen Arsip Digital</p>
                </div>
            </div>
        </AppLayout>
    );
}