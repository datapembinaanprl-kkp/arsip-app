<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FIX: Survey migration awal hanya punya id + timestamps.
// Migration ini menambah kolom yang dibutuhkan.
// Jalankan: php artisan migrate
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('surveys', 'judul')) {
                $table->string('judul')->after('user_id');
            }
            if (!Schema::hasColumn('surveys', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('surveys', 'fields')) {
                $table->json('fields')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('surveys', 'token_akses')) {
                $table->string('token_akses')->unique()->nullable()->after('fields');
            }
            if (!Schema::hasColumn('surveys', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('token_akses');
            }
            if (!Schema::hasColumn('surveys', 'batas_waktu')) {
                $table->timestamp('batas_waktu')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['user_id','judul','deskripsi','fields','token_akses','is_active','batas_waktu']);
        });
    }
};