<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'judul', 'nomor_dokumen', 'status', 'deadline',
        'catatan', 'alasan_revisi', 'assignee_id', 'created_by',
        'diajukan_at', 'disetujui_at', 'selesai_at',
    ];

    protected $casts = [
        'deadline'     => 'date',
        'diajukan_at'  => 'datetime',
        'disetujui_at' => 'datetime',
        'selesai_at'   => 'datetime',
    ];

    // ─── Status Config ────────────────────────────────────────────

    /** Label, warna, dan urutan kolom kanban */
    public const STATUSES = [
        'draft'     => ['label' => 'Draft',     'color' => 'slate',  'order' => 1],
        'diajukan'  => ['label' => 'Diajukan',  'color' => 'blue',   'order' => 2],
        'revisi'    => ['label' => 'Revisi',     'color' => 'orange', 'order' => 3],
        'disetujui' => ['label' => 'Disetujui', 'color' => 'green',  'order' => 4],
        'selesai'   => ['label' => 'Selesai',   'color' => 'purple', 'order' => 5],
    ];

    /**
     * Transisi status yang diperbolehkan per role.
     * key = status saat ini, value = status tujuan yang boleh dipilih
     */
    public const TRANSITIONS = [
        'staff' => [
            'draft'    => ['diajukan'],           // Staff mengajukan
            'revisi'   => ['diajukan'],            // Staff resubmit setelah revisi
        ],
        'direktur' => [
            'diajukan'  => ['disetujui', 'revisi'], // Direktur setuju atau kembalikan
            'disetujui' => ['selesai'],              // Direktur finalisasi
        ],
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class)->latest();
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'slate';
    }

    /** Cek apakah deadline sudah lewat */
    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && $this->deadline->isPast()
            && !in_array($this->status, ['selesai', 'disetujui']);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    /** Filter berdasarkan assignee (untuk tampilan staff — hanya dokumen miliknya) */
    public function scopeForStaff($query, int $userId)
    {
        return $query->where('assignee_id', $userId);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /** Ambil transisi yang tersedia untuk role tertentu */
    public function availableTransitions(string $role): array
    {
        return self::TRANSITIONS[$role][$this->status] ?? [];
    }
}