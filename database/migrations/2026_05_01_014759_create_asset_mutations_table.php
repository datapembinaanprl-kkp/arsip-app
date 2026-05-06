<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                  ->constrained('assets')
                  ->onDelete('cascade');
            $table->string('unit_asal');
            $table->string('unit_tujuan');
            $table->date('tanggal_mutasi');
            $table->string('no_berita_acara')->nullable();       // Nomor BA mutasi
            $table->text('keterangan')->nullable();
            $table->string('dokumen')->nullable();               // BA/SK mutasi
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamps();

            $table->index(['asset_id', 'tanggal_mutasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_mutations');
    }
};