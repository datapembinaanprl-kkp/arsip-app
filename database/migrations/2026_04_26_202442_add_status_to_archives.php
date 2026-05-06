<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            // Status dokumen untuk tracking progres
            if (! Schema::hasColumn('archives', 'status')) {
                $table->string('status')->default('aktif')->after('description');
                // Nilai: aktif | ditolak | diarsipkan
            }

            // Siapa yang terakhir mengubah status (untuk audit trail ringan)
            if (! Schema::hasColumn('archives', 'reviewed_by')) {
                $table->foreignId('reviewed_by')
                      ->nullable()
                      ->after('status')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            // Catatan/alasan saat status berubah
            if (! Schema::hasColumn('archives', 'catatan_review')) {
                $table->text('catatan_review')->nullable()->after('reviewed_by');
            }

            // Waktu review terakhir
            if (! Schema::hasColumn('archives', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('catatan_review');
            }

            // Kategori dokumen
            if (! Schema::hasColumn('archives', 'kategori')) {
                $table->string('kategori')->nullable()->after('description');
            }

            // Soft delete agar dokumen tidak hilang permanen
            if (! Schema::hasColumn('archives', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $columns = ['status', 'reviewed_by', 'catatan_review', 'reviewed_at', 'kategori', 'deleted_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('archives', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};