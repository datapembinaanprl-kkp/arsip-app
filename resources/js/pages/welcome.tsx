import { Head, Link } from "@inertiajs/react";

export default function Welcome({ auth }: { auth: { user?: { name: string } } }) {
    return (
        <>
            <Head title="Selamat Datang" />
            <div className="min-h-screen bg-gray-50 flex items-center justify-center">
                <div className="text-center">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">Web Arsip</h1>
                    <p className="text-gray-500 mb-8">Sistem Manajemen Arsip Digital</p>
                    <div className="flex items-center justify-center gap-4">
                        {auth.user ? (
                            <Link href={route("dashboard")} className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link href={route("login")} className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Log in
                                </Link>
                                <Link href={route("register")} className="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                    Register
                                </Link>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
