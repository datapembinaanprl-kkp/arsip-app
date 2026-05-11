import { Head, Link } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";

export default function TeamsIndex({ teams }: { teams: any[] }) {
    return (
        <AppLayout>
            <Head title="Teams" />
            <div className="flex flex-col gap-6">
                <h1 className="text-2xl font-semibold text-gray-900">Teams</h1>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-50">
                    {teams?.map((team: any) => (
                        <div key={team.id} className="px-6 py-4 flex items-center justify-between">
                            <span className="font-medium text-gray-900">{team.name}</span>
                            <Link href={route("teams.edit", team.id)} className="text-sm text-blue-600 hover:underline">
                                Settings
                            </Link>
                        </div>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
