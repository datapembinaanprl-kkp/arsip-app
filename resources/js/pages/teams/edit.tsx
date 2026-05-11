import { Head, useForm } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import InputError from "@/components/input-error";

export default function TeamsEdit({ team, availableRoles, permissions }: { team: any; availableRoles?: any[]; permissions?: any }) {
    const { data, setData, put, processing, errors } = useForm({
        name: team?.name ?? "",
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route("teams.update", team.id));
    };

    return (
        <AppLayout>
            <Head title="Team Settings" />
            <div className="flex flex-col gap-6 max-w-2xl">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Team Settings</h1>
                    <p className="text-sm text-gray-500 mt-0.5">{team?.name}</p>
                </div>
                <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <form onSubmit={submit} className="space-y-5">
                        <div className="space-y-2">
                            <Label htmlFor="name">Team Name</Label>
                            <Input id="name" value={data.name} onChange={e => setData("name", e.target.value)} required />
                            <InputError message={errors.name} />
                        </div>
                        <Button type="submit" disabled={processing}>
                            {processing ? "Saving..." : "Save"}
                        </Button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
