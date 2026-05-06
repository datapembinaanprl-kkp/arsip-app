<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMutation extends Model
{
    protected $fillable = [
        'asset_id', 'unit_asal', 'unit_tujuan',
        'tanggal_mutasi', 'no_berita_acara', 'keterangan',
        'dokumen', 'created_by',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDokumenUrlAttribute(): ?string
    {
        return $this->dokumen ? asset('storage/' . $this->dokumen) : null;
    }
}