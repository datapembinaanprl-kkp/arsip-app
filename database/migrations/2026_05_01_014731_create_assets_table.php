<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();             // Kode unik aset BMN
            $table->string('nama_barang');
            $table->enum('kategori', [
                'Tanah & Bangunan',
                'Kendaraan',
                'Peralatan & Mesin',
                'Aset Tetap Lainnya',
            ]);
            $table->string('merk_tipe')->nullable();             // Merk / tipe barang
            $table->string('no_seri')->nullable();               // Nomor seri / NUP
            $table->year('tahun_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 15, 2)->default(0); // Nilai dalam rupiah
            $table->enum('kondisi', [
                'Baik',
                'Rusak Ringan',
                'Rusak Berat',
            ])->default('Baik');
            $table->string('lokasi');                            // Lokasi fisik aset
            $table->string('unit_pengguna');                     // Satuan kerja / unit pemakai
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();                  // Path foto aset
            $table->string('dokumen')->nullable();               // Path dokumen pendukung (PDF/dll)
            $table->timestamps();

            $table->index(['kategori', 'kondisi']);
            $table->index('unit_pengguna');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};