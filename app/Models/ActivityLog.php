<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'modul', 'aksi', 'nama_item', 'url', 'ip_adress',
    ];

    // Tidak perlu updated_at
    const UPDATED_AT = null;

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors ────────────────────────────────────────────────

    /** Inisial nama user untuk avatar */
    public function getInitialsAttribute(): string
    {
        if (!$this->user) return '?';
        $words = explode(' ', $this->user->name);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    /** Warna avatar per modul */
    public function getModulColorAttribute(): string
    {
        return match($this->modul) {
            'aset'       => '#2563eb',
            'dokumen'    => '#7c3aed',
            'organisasi' => '#059669',
            default      => '#64748b',
        };
    }

    /** Label modul yang ditampilkan */
    public function getModulLabelAttribute(): string
    {
        return match($this->modul) {
            'aset'       => 'Aset BMN',
            'dokumen'    => 'Dokumen',
            'organisasi' => 'Struktur Organisasi',
            default      => ucfirst($this->modul),
        };
    }

    // ─── Static Helper ────────────────────────────────────────────

    /**
     * Catat aktivitas dari mana saja.
     * Contoh: ActivityLog::record('aset', 'Menambahkan', $asset->nama_barang, route('assets.show', $asset));
     */
    public static function record(
        string  $modul,
        string  $aksi,
        string  $namaItem,
        ?string $url = null
    ): self {
        return self::create([
            'user_id'    => auth()->id(),
            'modul'      => $modul,
            'aksi'       => $aksi,
            'nama_item'  => $namaItem,
            'url'        => $url,
            'ip_address' => request()->ip(),
        ]);
    }
}