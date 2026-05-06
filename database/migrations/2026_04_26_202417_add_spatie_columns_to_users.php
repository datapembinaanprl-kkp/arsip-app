<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Spatie membutuhkan users.id sebagai integer (bukan UUID).
// Migration ini memastikan kolom yang dibutuhkan sudah ada.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom is_active jika belum ada
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('email');
            }

            // Hapus kolom 'role' lama (plain string) jika ada,
            // karena sekarang pakai tabel Spatie
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('staf')->after('email');
            }
        });
    }
};