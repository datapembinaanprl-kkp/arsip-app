import type { User as AuthUser } from '@/types/auth'


// ─── Re-export auth User dengan extend field baru ─────────────────────────────

export interface User {
    id:                  number
    name:                string
    email:               string
    role:                string
    status:              string
    is_active:           boolean      // kolom lama masih ada
    phone:               string | null
    avatar:              string | null
    avatar_url:          string
    nip:                 string | null
    pangkat_golongan:    string | null
    jabatan_fungsional:  string | null // ← nama kolom aktual
    SPT:                 string | null // ← uppercase
    SKP:                 string | null // ← uppercase
    last_login:          string | null // ← nama kolom aktual
    tim_kerja_id:        number | null
    email_verified_at:   string | null
    created_at:          string
    updated_at:          string
}

// ─── Enums ────────────────────────────────────────────────────

export type DocumentStatus =
    | 'draft'
    | 'review'
    | 'approved'
    | 'rejected'
    | 'archived'

// ─── Tim Kerja ────────────────────────────────────────────────

export interface TimKerja {
    id:         number
    nama:       string
    kode:       string
    deskripsi:  string | null
    is_active:  boolean
    created_at: string
    updated_at: string
}

// ─── Document ─────────────────────────────────────────────────

export interface Document {
    id:            number
    judul:         string
    nomor_dokumen: string | null
    deadline:      string | null
    catatan:       string | null
    status:        DocumentStatus
    assignee_id:   number
    created_by:    number
    tim_kerja_id:  number | null
    assignee?:     Pick<User, 'id' | 'name'> | null
    creator?:      Pick<User, 'id' | 'name'> | null
    tim_kerja?:    Pick<TimKerja, 'id' | 'nama' | 'kode'> | null
    histories?:    DocumentHistory[]
    created_at:    string
    updated_at:    string
}

export interface DocumentHistory {
    id:              number
    document_id:     number
    changed_by:      number
    field:           string
    old_value:       string | null
    new_value:       string | null
    changed_by_user?: Pick<User, 'id' | 'name'>
    created_at:      string
}

// ─── Pagination ───────────────────────────────────────────────

export interface PaginationLink {
    url:    string | null
    label:  string
    active: boolean
}

export interface Paginated<T> {
    data:          T[]
    current_page:  number
    last_page:     number
    per_page:      number
    total:         number
    from:          number | null
    to:            number | null
    links:         PaginationLink[]
    next_page_url: string | null
    prev_page_url: string | null
}