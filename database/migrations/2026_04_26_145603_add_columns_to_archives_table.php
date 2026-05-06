<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FIX: Tambah kolom yang dibutuhkan Dashboard & Laporan controller
// Jalankan: php artisan migrate
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            if (!Schema::hasColumn('archives', 'tipe_dokumen')) {
                $table->string('tipe_dokumen')->default('lainnya')->after('description');
            }
            if (!Schema::hasColumn('archives', 'nomor_dokumen')) {
                $table->string('nomor_dokumen')->nullable()->after('tipe_dokumen');
            }
            if (!Schema::hasColumn('archives', 'tanggal_dokumen')) {
                $table->date('tanggal_dokumen')->nullable()->after('nomor_dokumen');
            }
            if (!Schema::hasColumn('archives', 'tanggal_retensi')) {
                $table->date('tanggal_retensi')->nullable()->after('tanggal_dokumen');
            }
            if (!Schema::hasColumn('archives', 'status')) {
                $table->string('status')->default('aktif')->after('tanggal_retensi');
            }
            if (!Schema::hasColumn('archives', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_dokumen','nomor_dokumen','tanggal_dokumen',
                'tanggal_retensi','status','mime_type',
            ]);
        });
    }
};