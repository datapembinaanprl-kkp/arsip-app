<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'judul',
        'nomor_dokumen',
        'deadline',
        'catatan',
        'status',
        'assignee_id',
        'created_by',
        'tim_kerja_id',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    const STATUS_OPTIONS = ['draft', 'review', 'approved', 'rejected', 'archived'];

    // ─── Relations ────────────────────────────────────────────

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function timKerja(): BelongsTo
    {
        return $this->belongsTo(TimKerja::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    // ─── Scopes ───────────────────────────────────────────────

    /**
     * Filter dokumen berdasarkan role user yang login.
     *
     * admin/direktur  → semua dokumen
     * kepala_tim_kerja → dokumen dari timnya
     * staff           → dokumen yang di-assign ke dia
     */
    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        return match (true) {
            $user->hasAnyRole(['admin', 'direktur']) => $query,

            $user->hasRole('kepala_tim_kerja') => $query->where(
                'tim_kerja_id', $user->tim_kerja_id
            ),

            default => $query->where('assignee_id', $user->id),
        };
    }
}