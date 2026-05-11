import { Head, useForm } from "@inertiajs/react";
import InputError from "@/components/input-error";
import PasswordInput from "@/components/password-input";
import TextLink from "@/components/text-link";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AuthLayout from "@/layouts/auth-layout";

export default function Login({ status, canResetPassword, canRegister }: {
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false as boolean,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route("login"), { onFinish: () => reset("password") });
    };

    return (
        <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
            <Head title="Log in" />
            <form onSubmit={submit} className="space-y-6">
                <div className="space-y-2">
                    <Label htmlFor="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={e => setData("email", e.target.value)}
                        autoComplete="email"
                        required
                    />
                    <InputError message={errors.email} />
                </div>
                <div className="space-y-2">
                    <div className="flex items-center justify-between">
                        <Label htmlFor="password">Password</Label>
                        {canResetPassword && (
                            <TextLink href={route("password.request")} className="text-sm">
                                Forgot password?
                            </TextLink>
                        )}
                    </div>
                    <PasswordInput
                        id="password"
                        value={data.password}
                        onChange={e => setData("password", e.target.value)}
                        autoComplete="current-password"
                        required
                    />
                    <InputError message={errors.password} />
                </div>
                <div className="flex items-center gap-2">
                    <Checkbox
                        id="remember"
                        checked={data.remember}
                        onCheckedChange={checked => setData("remember", !!checked)}
                    />
                    <Label htmlFor="remember" className="cursor-pointer font-normal">
                        Remember me
                    </Label>
                </div>
                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? "Logging in..." : "Log in"}
                </Button>
                {canRegister && (
                    <p className="text-center text-sm text-muted-foreground">
                        <TextLink href={route("register")}>Sign up</TextLink>
                    </p>
                )}
            </form>
            {status && (
                <div className="mt-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}