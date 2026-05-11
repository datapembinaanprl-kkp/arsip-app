import { Head } from '@inertiajs/react';
export default function SurveyPublicShow({ survey }: { survey: any }) {
    return <><Head title={survey?.title} /><div className="min-h-screen bg-gray-50 flex items-center justify-center p-6"><div className="bg-white rounded-xl border border-gray-200 shadow-sm p-8 max-w-xl w-full"><h1 className="text-2xl font-semibold text-gray-900">{survey?.title}</h1></div></div></>;
}
