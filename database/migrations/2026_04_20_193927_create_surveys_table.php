<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (Schema::hasTable('surveys')) {
        // Tambah kolom yang belum ada saja
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'judul')) {
                $table->string('judul');
            }
            if (!Schema::hasColumn('surveys', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
            if (!Schema::hasColumn('surveys', 'token')) {
                $table->string('token', 64)->unique();
            }
            if (!Schema::hasColumn('surveys', 'status')) {
                $table->enum('status', ['draft', 'aktif', 'tutup'])->default('draft');
            }
            if (!Schema::hasColumn('surveys', 'batas_waktu')) {
                $table->timestamp('batas_waktu')->nullable();
            }
            if (!Schema::hasColumn('surveys', 'created_by')) {
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            }
        });
        return;
    }

    Schema::create('surveys', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        $table->string('token', 64)->unique();
        $table->enum('status', ['draft', 'aktif', 'tutup'])->default('draft');
        $table->timestamp('batas_waktu')->nullable();
        $table->foreignId('created_by')
              ->constrained('users')
              ->onDelete('cascade');
        $table->timestamps();

        $table->index(['status', 'batas_waktu']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};