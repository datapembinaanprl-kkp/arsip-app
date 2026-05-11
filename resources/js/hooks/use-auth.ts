import { usePage } from '@inertiajs/react';

// ─── Permission map per role ──────────────────────────────────────────────────
// Sesuaikan dengan roles yang ada di backend (UserRole enum)

const ROLE_PERMISSIONS: Record<string, string[]> = {
    admin: ['*'],
    kepala_tim_kerja: [
        'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
        'documents.approve', 'categories.view', 'categories.manage',
        'users.view', 'tim-kerja.view', 'tim-kerja.manage',
    ],
    staff: [
        'documents.view', 'documents.create', 'documents.edit',
        'categories.view', 'tim-kerja.view',
    ],
    viewer: [
        'documents.view',
    ],
};

// ─── Hook ─────────────────────────────────────────────────────────────────────

export function useAuth() {
    const { auth } = usePage().props as {
        auth: { user?: { id: number; name: string; email: string; role?: string } }
    };

    const user = auth?.user ?? null;
    const role = user?.role ?? 'viewer';

    /**
     * Cek apakah user punya permission.
     * Mendukung wildcard: 'documents.*' → cocok dengan 'documents.view', 'documents.edit', dst.
     */
    const can = (permission: string): boolean => {
        const permissions = ROLE_PERMISSIONS[role] ?? [];

        // Admin → akses semua
        if (permissions.includes('*')) return true;

        // Match exact
        if (permissions.includes(permission)) return true;

        // Match wildcard — misal permission 'documents.*' cocok dengan 'documents.view'
        const [resource, action] = permission.split('.');
        if (permissions.includes(`${resource}.*`)) return true;

        // Cek jika role punya wildcard untuk resource yang diminta
        if (action && permissions.some(p => p === `${resource}.*`)) return true;

        return false;
    };

    const hasRole = (...roles: string[]): boolean => roles.includes(role);

    return { user, role, can, hasRole };
}