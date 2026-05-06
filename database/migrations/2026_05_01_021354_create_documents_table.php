<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('nomor_dokumen')->unique()->nullable();
            $table->enum('status', [
                'draft',        // Baru dibuat oleh staff
                'diajukan',     // Diajukan ke direktur untuk direview
                'disetujui',    // Disetujui direktur
                'revisi',       // Dikembalikan oleh direktur, perlu diperbaiki
                'selesai',      // Final/arsip
            ])->default('draft');
            $table->date('deadline')->nullable();
            $table->text('catatan')->nullable();          // Catatan umum
            $table->text('alasan_revisi')->nullable();    // Diisi saat dikembalikan
            $table->foreignId('assignee_id')             // Staff yang mengerjakan
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('created_by')              // Siapa yang membuat dokumen
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamp('diajukan_at')->nullable();
            $table->timestamp('disetujui_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'deadline']);
            $table->index('assignee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};