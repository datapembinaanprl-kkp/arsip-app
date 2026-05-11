import { Head } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import AppearanceToggleTab from "@/components/appearance-tabs";

export default function Appearance() {
    return (
        <AppLayout>
            <Head title="Appearance" />
            <div className="flex flex-col gap-6 max-w-2xl">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Appearance</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Customize the appearance of the app</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <AppearanceToggleTab />
                </div>
            </div>
        </AppLayout>
    );
}
