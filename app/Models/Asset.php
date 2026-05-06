<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori', 'merk_tipe',
        'no_seri', 'tahun_perolehan', 'nilai_perolehan', 'kondisi',
        'lokasi', 'unit_pengguna', 'keterangan', 'foto', 'dokumen',
    ];

    protected $casts = [
        'nilai_perolehan' => 'decimal:2',
        'tahun_perolehan' => 'integer',
    ];

    // ─── Constants ────────────────────────────────────────────────

    public const KATEGORI = [
        'Tanah & Bangunan',
        'Kendaraan',
        'Peralatan & Mesin',
        'Aset Tetap Lainnya',
    ];

    public const KONDISI = [
        'Baik'         => 'success',  // badge color mapping
        'Rusak Ringan' => 'warning',
        'Rusak Berat'  => 'danger',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function mutations(): HasMany
    {
        return $this->hasMany(AssetMutation::class)->latest();
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default-asset.png');
    }

    public function getDokumenUrlAttribute(): ?string
    {
        return $this->dokumen ? asset('storage/' . $this->dokumen) : null;
    }

    /** Format nilai perolehan ke Rupiah */
    public function getNilaiFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nilai_perolehan, 0, ',', '.');
    }

    /** Warna badge kondisi */
    public function getKondisiBadgeAttribute(): string
    {
        return self::KONDISI[$this->kondisi] ?? 'secondary';
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeSearch($query, ?string $keyword)
    {
        return $query->when($keyword, fn($q) =>
            $q->where('nama_barang', 'like', "%{$keyword}%")
              ->orWhere('kode_barang', 'like', "%{$keyword}%")
              ->orWhere('unit_pengguna', 'like', "%{$keyword}%")
        );
    }

    public function scopeFilterKategori($query, ?string $kategori)
    {
        return $query->when($kategori, fn($q) => $q->where('kategori', $kategori));
    }

    public function scopeFilterKondisi($query, ?string $kondisi)
    {
        return $query->when($kondisi, fn($q) => $q->where('kondisi', $kondisi));
    }
}