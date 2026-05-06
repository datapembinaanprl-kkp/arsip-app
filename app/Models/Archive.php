<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
    use SoftDeletes;

    // Status yang mungkin
    const STATUS_AKTIF     = 'aktif';
    const STATUS_DITOLAK   = 'ditolak';
    const STATUS_DIARSIPKAN = 'diarsipkan';

    protected $fillable = [
        'user_id',
        'title',
        'file',
        'description',
        'kategori',
        'status',
        'reviewed_by',
        'catatan_review',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────

    // FIX: Gunakan scope ini di DashboardController
    // Sebelumnya: where("status" = aktif) → error karena kolom belum ada
    // Sekarang kolom ada, dan pakai konstanta bukan hardcode string
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    // Kompatibel dengan kode lama yang pakai ->active()
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    // Dokumen milik user tertentu
    public function scopeMilik($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Relasi ───────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Helpers ──────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif'      => 'Aktif',
            'ditolak'    => 'Ditolak',
            'diarsipkan' => 'Diarsipkan',
            default      => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'aktif'      => 'green',
            'ditolak'    => 'red',
            'diarsipkan' => 'gray',
            default      => 'gray',
        };
    }

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }
}