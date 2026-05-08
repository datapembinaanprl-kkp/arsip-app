<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimKerja extends Model
{
    protected $fillable = ['nama', 'kode', 'deskripsi', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function kepala(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class)
                    ->whereHas('roles', fn ($q) => $q->where('name', 'kepala_tim_kerja'));
    }
}