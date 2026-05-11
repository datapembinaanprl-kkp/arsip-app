import { Head, useForm } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import InputError from "@/components/input-error";
import PasswordInput from "@/components/password-input";

export default function Security({ sessions }: { sessions?: any[] }) {
    const { data, setData, put, processing, errors, reset } = useForm({
        current_password: "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route("password.update"), {
            onFinish: () => reset("current_password", "password", "password_confirmation"),
        });
    };

    return (
        <AppLayout>
            <Head title="Security" />
            <div className="flex flex-col gap-6 max-w-2xl">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Security</h1>
                    <p className="text-sm text-gray-500 mt-0.5">Update your password</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <form onSubmit={submit} className="space-y-5">
                        <div className="space-y-2">
                            <Label htmlFor="current_password">Current Password</Label>
                            <PasswordInput id="current_password" value={data.current_password} onChange={e => setData("current_password", e.target.value)} required />
                            <InputError message={errors.current_password} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="password">New Password</Label>
                            <PasswordInput id="password" value={data.password} onChange={e => setData("password", e.target.value)} required />
                            <InputError message={errors.password} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="password_confirmation">Confirm Password</Label>
                            <PasswordInput id="password_confirmation" value={data.password_confirmation} onChange={e => setData("password_confirmation", e.target.value)} required />
                            <InputError message={errors.password_confirmation} />
                        </div>
                        <Button type="submit" disabled={processing}>
                            {processing ? "Updating..." : "Update password"}
                        </Button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
