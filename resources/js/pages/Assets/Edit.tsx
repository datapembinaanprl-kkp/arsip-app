import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
export default function AssetsEdit({ asset }: { asset: any }) {
    return <AppLayout><Head title="Edit Aset" /><div className="p-6"><h1 className="text-2xl font-semibold">Edit Aset: {asset?.nama}</h1></div></AppLayout>;
}
