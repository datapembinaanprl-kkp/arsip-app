import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
export default function AssetsShow({ asset }: { asset: any }) {
    return <AppLayout><Head title="Detail Aset" /><div className="p-6"><h1 className="text-2xl font-semibold">{asset?.nama}</h1></div></AppLayout>;
}
