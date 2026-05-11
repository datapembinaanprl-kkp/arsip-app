import { Head, useForm } from '@inertiajs/react';
import { REGEXP_ONLY_DIGITS } from 'input-otp';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { OTP_MAX_LENGTH } from '@/hooks/use-two-factor-auth';
import AuthLayout from '@/layouts/auth-layout';

export default function TwoFactorChallenge() {
    const [showRecoveryInput, setShowRecoveryInput] = useState(false);

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        code: '',
        recovery_code: '',
    });

    const toggleRecoveryMode = () => {
        setShowRecoveryInput(v => !v);
        clearErrors();
        reset();
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('two-factor.login'), {
            onError: () => reset('code', 'recovery_code'),
        });
    };

    const title       = showRecoveryInput ? 'Recovery Code' : 'Authentication Code';
    const description = showRecoveryInput
        ? 'Please confirm access to your account by entering one of your emergency recovery codes.'
        : 'Enter the authentication code provided by your authenticator application.';
    const toggleText  = showRecoveryInput
        ? 'login using an authentication code'
        : 'login using a recovery code';

    return (
        <AuthLayout title={title} description={description}>
            <Head title="Two-factor authentication" />

            <form onSubmit={submit} className="space-y-4">
                {showRecoveryInput ? (
                    <div>
                        <Input
                            name="recovery_code"
                            type="text"
                            placeholder="Enter recovery code"
                            value={data.recovery_code}
                            onChange={e => setData('recovery_code', e.target.value)}
                            autoFocus={true}
                            required
                        />
                        <InputError message={errors.recovery_code} className="mt-1" />
                    </div>
                ) : (
                    <div className="flex flex-col items-center justify-center space-y-3 text-center">
                        <div className="flex w-full items-center justify-center">
                            <InputOTP
                                name="code"
                                maxLength={OTP_MAX_LENGTH}
                                value={data.code}
                                onChange={value => setData('code', value)}
                                disabled={processing}
                                pattern={REGEXP_ONLY_DIGITS}
                                autoFocus={true}
                            >
                                <InputOTPGroup>
                                    {Array.from({ length: OTP_MAX_LENGTH }, (_, i) => (
                                        <InputOTPSlot key={i} index={i} />
                                    ))}
                                </InputOTPGroup>
                            </InputOTP>
                        </div>
                        <InputError message={errors.code} />
                    </div>
                )}

                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? 'Memproses...' : 'Continue'}
                </Button>

                <div className="text-center text-sm text-muted-foreground">
                    <span>or you can </span>
                    <button
                        type="button"
                        onClick={toggleRecoveryMode}
                        className="cursor-pointer text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors hover:decoration-current"
                    >
                        {toggleText}
                    </button>
                </div>
            </form>
        </AuthLayout>
    );
}