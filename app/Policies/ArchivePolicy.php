<?php

namespace App\Policies;

use App\Models\Archive;
use App\Models\User;

class ArchivePolicy
{
    // Admin bisa melakukan apapun
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true; // Bypass semua pengecekan
        }
        return null; // Lanjut ke method berikutnya
    }

    // Siapa yang boleh melihat daftar dokumen
    public function viewAny(User $user): bool
    {
        return $user->can('dokumen.lihat');
    }

    // Siapa yang boleh melihat detail satu dokumen
    public function view(User $user, Archive $archive): bool
    {
        if (! $user->can('dokumen.lihat')) {
            return false;
        }

        // Staf hanya bisa lihat dokumen milik sendiri
        if ($user->hasRole('staf')) {
            return $archive->user_id === $user->id;
        }

        return true;
    }

    // Siapa yang boleh upload dokumen baru
    public function create(User $user): bool
    {
        return $user->can('dokumen.upload');
    }

    // Siapa yang boleh edit dokumen
    public function update(User $user, Archive $archive): bool
    {
        if (! $user->can('dokumen.edit')) {
            return false;
        }

        // Staf hanya edit dokumen sendiri yang statusnya 'ditolak' (untuk revisi)
        if ($user->hasRole('staf')) {
            return $archive->user_id === $user->id && $archive->isDitolak();
        }

        return true;
    }

    // Siapa yang boleh hapus dokumen (soft delete)
    public function delete(User $user, Archive $archive): bool
    {
        return $user->can('dokumen.hapus');
    }

    // Siapa yang boleh restore dokumen yang ter-soft-delete
    public function restore(User $user, Archive $archive): bool
    {
        return $user->hasAnyRole(['admin', 'direktur']);
    }
}