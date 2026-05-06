<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // ← Spatie

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // FIX: Tambah HasRoles

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relasi ───────────────────────────────────────────
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }

    // ─── Helper untuk Blade ───────────────────────────────
    // Cek apakah user punya salah satu dari array role
    // (Spatie sudah punya hasRole() & hasAnyRole() bawaan)

    // Nama tampilan role dalam Bahasa Indonesia
    public function getRoleLabelAttribute(): string
    {
        return match ($this->getRoleNames()->first()) {
            'admin'      => 'Administrator',
            'staf'       => 'Staf',
            'supervisor' => 'Supervisor',
            'direktur'   => 'Direktur',
            default      => 'Pengguna',
        };
    }
}