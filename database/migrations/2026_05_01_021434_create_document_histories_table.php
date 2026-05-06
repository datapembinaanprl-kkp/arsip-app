<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Audit trail setiap perubahan status dokumen
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                  ->constrained('documents')
                  ->onDelete('cascade');
            $table->string('status_dari')->nullable();
            $table->string('status_ke');
            $table->text('catatan')->nullable();
            $table->foreignId('changed_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();

            $table->index(['document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
};