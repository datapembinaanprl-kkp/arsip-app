import { Head, useForm, usePage } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import InputError from "@/components/input-error";

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { auth } = usePage().props as any;
    const { data, setData, patch, processing, errors } = useForm({
        name: auth.user?.name ?? "",
        email: auth.user?.email ?? "",
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route("profile.update"));
    };

    return (
        <AppLayout>
            <Head title="Profile" />
            <div className="flex flex-col gap-6 max-w-2xl">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Profile</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Update your profile information</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <form onSubmit={submit} className="space-y-5">
                        <div className="space-y-2">
                            <Label htmlFor="name">Name</Label>
                            <Input id="name" value={data.name} onChange={e => setData("name", e.target.value)} required />
                            <InputError message={errors.name} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" type="email" value={data.email} onChange={e => setData("email", e.target.value)} required />
                            <InputError message={errors.email} />
                        </div>
                        {mustVerifyEmail && auth.user?.email_verified_at === null && (
                            <p className="text-sm text-yellow-600">Your email is unverified.</p>
                        )}
                        {status === "profile-updated" && (
                            <p className="text-sm text-green-600">Profile updated successfully.</p>
                        )}
                        <Button type="submit" disabled={processing}>
                            {processing ? "Saving..." : "Save changes"}
                        </Button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
