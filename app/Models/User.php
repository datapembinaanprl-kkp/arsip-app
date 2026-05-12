<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; 

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'avatar',
        'nip',
        'pangkat_golongan',
        'jabatan_fungsional',
        'SPT',
        'SKP',
        'last_login',
        'tim_kerja_id',
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
            'last_login'        => 'datetime',
        ];
    }

    public function hasCustomRole(UserRole|string ...$roles): bool
    {
        $values = array_map(
            fn($r) => $r instanceof UserRole ? $r->value : $r,
            $roles
        );
        $current = $this->role instanceof UserRole
            ? $this->role->value
            : $this->role;

        return in_array($current, $values);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
    }

    public function getJabatanFungsionalAttribute(): ?string
    {
        return $this->attributes['jabatan_fungsional'] ?? null;
    }
}