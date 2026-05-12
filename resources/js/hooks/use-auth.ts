import { usePage } from '@inertiajs/react';

interface AuthUser {
    id: number;
    name: string;
    email: string;
    avatar_url?: string;
    roles?: string[];
    permissions?: string[];
    tim_kerja?: {
        id: number;
        nama: string;
        kode: string;
    } | null;
}

interface Auth {
    user?: AuthUser | null;
}


// ─── Permission map per role ──────────────────────────────────────────────────
// Sesuaikan dengan roles yang ada di backend (UserRole enum)

const ROLE_PERMISSIONS: Record<string, string[]> = {
    admin: ['*'],
    direktur: ['*'],
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
        auth: Auth;
    };

    const user = auth?.user ?? null;
    const roles = user?.roles ?? [];
    const permissions = user?.permissions ?? [];

    // primary role = yang pertama kali muncul di array roles (sesuai urutan di database)
    const role = roles[0] ?? 'null';

    /**
     * Cek apakah user punya permission.
     * Mendukung wildcard: 'documents.*' → cocok dengan 'documents.view', 'documents.edit', dst.
     */
       const can = (permission: string): boolean => {
        if (!permissions.length) return false;

        // Exact match
        if (permissions.includes(permission)) return true;

        // Wildcard check: 'dokumen.*' cocok dengan 'dokumen.viewAny'
        const [resource] = permission.split('.');
        if (permissions.includes(`${resource}.*`)) return true;

        // Reverse wildcard: caller pakai 'dokumen.*', cek apakah ada permission dengan prefix itu
        if (permission.endsWith('.*')) {
            const prefix = permission.slice(0, -2);
            return permissions.some(p => p.startsWith(`${prefix}.`));
        }

        return false;
    };

     const hasRole = (...check: string[]): boolean =>
        check.some(r => roles.includes(r));

    return { user, role, roles, permissions, can, hasRole };
}