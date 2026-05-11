import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
export default function OrgStructureEdit({ member }: { member: any }) {
    return <AppLayout><Head title="Edit Anggota" /><div className="p-6"><h1 className="text-2xl font-semibold">Edit: {member?.name}</h1></div></AppLayout>;
}
