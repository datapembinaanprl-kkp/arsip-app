<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constraind('users')
                ->onDelete('set null');
            
            $table->string('modul');                        //aset', 'dokumen', 'organisasi', dll
            $table->string('aksi');                         // 'Menambahkan', 'Memperbarui', 'Menghapus', dll
            $table->string('nama_item');                    // Nama aset/dokumen/anggota yang diubah
            $table->string('url')->nullable();              // URL ke detail item (jika masih ada)
            $table->string('ip_address',45)-> nullable();
            $table->timestamps();

            $table->index(['modul','created_at']);
            $table->index(['user_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
