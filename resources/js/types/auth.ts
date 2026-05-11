// ─── Roles & Permissions ──────────────────────────────────────

export type UserRole =
    | 'admin'
    | 'direktur'
    | 'kepala_tim_kerja'
    | 'staff'

// ─── User ─────────────────────────────────────────────────────

/**
 * Full User model — dipakai di halaman User Management
 */
export type User = {
    id:                number
    name:              string
    email:             string
    email_verified_at: string | null
    role:              UserRole
    phone:             string | null
    avatar:            string | null
    avatar_url:        string
    tim_kerja_id:      number | null
    last_login_at:     string | null
    two_factor_enabled?: boolean
    created_at:        string
    updated_at:        string
}

/**
 * AuthUser — subset User yang di-share via Inertia (HandleInertiaRequests)
 * Hanya field yang aman dan dibutuhkan frontend
 */
export type AuthUser = {
    id:          number
    name:        string
    email:       string
    role:        UserRole
    avatar_url:  string
    permissions: string[]
    tim_kerja: {
        id:   number
        nama: string
        kode: string
    } | null
}

export type Auth = {
    user: AuthUser | null
}

// ─── Two Factor ───────────────────────────────────────────────

export type TwoFactorSetupData = {
    svg: string
    url: string
}

export type TwoFactorSecretKey = {
    secretKey: string
}