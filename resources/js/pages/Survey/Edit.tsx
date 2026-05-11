import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
export default function SurveyEdit({ survey }: { survey: any }) {
    return <AppLayout><Head title="Edit Survey" /><div className="p-6"><h1 className="text-2xl font-semibold">Edit Survey: {survey?.title}</h1></div></AppLayout>;
}
