import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
export default function SurveyShow({ survey }: { survey: any }) {
    return <AppLayout><Head title="Detail Survey" /><div className="p-6"><h1 className="text-2xl font-semibold">{survey?.title}</h1></div></AppLayout>;
}
