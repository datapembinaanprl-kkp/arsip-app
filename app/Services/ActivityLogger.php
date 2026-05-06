<?php

namespace App\Services;

use App\Models\ActivityLog;

/**
 * Service helper — import class ini di controller manapun
 * untuk mencatat aktivitas dengan satu baris kode.
 */
class ActivityLogger
{
    // ─── Aset BMN ─────────────────────────────────────────────────

    public static function asetDibuat(string $namaAset, int $asetId): void
    {
        ActivityLog::record('aset', 'Menambahkan aset', $namaAset,
            route('assets.show', $asetId));
    }

    public static function asetDiperbarui(string $namaAset, int $asetId): void
    {
        ActivityLog::record('aset', 'Memperbarui aset', $namaAset,
            route('assets.show', $asetId));
    }

    public static function asetDihapus(string $namaAset): void
    {
        ActivityLog::record('aset', 'Menghapus aset', $namaAset);
    }

    public static function asetDimutasi(string $namaAset, string $unitTujuan, int $asetId): void
    {
        ActivityLog::record('aset', "Memutasi aset ke {$unitTujuan}", $namaAset,
            route('assets.show', $asetId));
    }

    // ─── Dokumen / Kanban ─────────────────────────────────────────

    public static function dokumenDibuat(string $judulDokumen, int $docId): void
    {
        ActivityLog::record('dokumen', 'Membuat dokumen', $judulDokumen,
            route('documents.show', $docId));
    }

    public static function dokumenDiperbarui(string $judulDokumen, int $docId): void
    {
        ActivityLog::record('dokumen', 'Memperbarui dokumen', $judulDokumen,
            route('documents.show', $docId));
    }

    public static function dokumenDihapus(string $judulDokumen): void
    {
        ActivityLog::record('dokumen', 'Menghapus dokumen', $judulDokumen);
    }

    public static function dokumenStatusBerubah(string $judulDokumen, string $statusBaru, int $docId): void
    {
        $labelStatus = \App\Models\Document::STATUSES[$statusBaru]['label'] ?? $statusBaru;
        ActivityLog::record('dokumen', "Mengubah status ke {$labelStatus}", $judulDokumen,
            route('documents.show', $docId));
    }

    // ─── Struktur Organisasi ──────────────────────────────────────

    public static function organisasiDibuat(string $nama): void
    {
        ActivityLog::record('organisasi', 'Menambahkan anggota', $nama,
            route('organizational-structure.index'));
    }

    public static function organisasiDiperbarui(string $nama): void
    {
        ActivityLog::record('organisasi', 'Memperbarui anggota', $nama,
            route('organizational-structure.index'));
    }

    public static function organisasiDihapus(string $nama): void
    {
        ActivityLog::record('organisasi', 'Menghapus anggota', $nama);
    }

    // ─── Generic (untuk modul baru di masa depan) ─────────────────

    public static function log(string $modul, string $aksi, string $namaItem, ?string $url = null): void
    {
        ActivityLog::record($modul, $aksi, $namaItem, $url);
    }
}