import { Head, useForm } from '@inertiajs/react';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

export default function VerifyEmail({ status }: { status?: string }) {
    const { post, processing } = useForm({});

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('verification.send'));
    };

    const logout = () => {
        useForm({}).post(route('logout'));
    };

    return (
        <AuthLayout title="Verify email" description="Please verify your email address by clicking the link we sent you">
            <Head title="Email Verification" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? 'Sending...' : 'Resend verification email'}
                </Button>

                <p className="text-center text-sm text-muted-foreground">
                    <TextLink href={route('logout')} method="post" as="button">
                        Log out
                    </TextLink>
                </p>
            </form>
        </AuthLayout>
    );
}