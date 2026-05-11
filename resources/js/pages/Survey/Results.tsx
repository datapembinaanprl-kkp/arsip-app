import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
export default function SurveyResults({ survey, submissions }: { survey: any; submissions: any }) {
    return <AppLayout><Head title="Hasil Survey" /><div className="p-6"><h1 className="text-2xl font-semibold">Hasil: {survey?.title}</h1><p className="text-gray-500 mt-1">{submissions?.total} respon</p></div></AppLayout>;
}
