<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Survey extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'token', 'status', 'batas_waktu', 'created_by',
    ];

    protected $casts = [
        'batas_waktu' => 'datetime',
    ];

    // ─── Boot: auto-generate token ────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (Survey $survey) {
            $survey->token ??= Str::random(32);
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SurveySubmission::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /** Cek apakah survey masih bisa menerima respons */
    public function isOpen(): bool
    {
        if ($this->status !== 'aktif') return false;
        if ($this->batas_waktu && $this->batas_waktu->isPast()) return false;
        return true;
    }

    /** URL publik yang bisa dibagikan */
    public function getPublikUrlAttribute(): string
    {
        return route('survey.public', $this->token);
    }

    /** Label status dengan warna */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'aktif'  => ['label' => 'Aktif',  'color' => 'green'],
            'tutup'  => ['label' => 'Tutup',  'color' => 'red'],
            default  => ['label' => 'Draft',  'color' => 'slate'],
        };
    }
}