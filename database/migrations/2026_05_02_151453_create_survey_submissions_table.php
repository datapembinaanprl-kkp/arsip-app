<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (Schema::hasTable('survey_submissions')) {
        return;
    }

    Schema::create('survey_submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('survey_id')
              ->constrained('surveys')
              ->onDelete('cascade');
        $table->string('nama_responden');
        $table->string('instansi')->nullable();
        $table->string('no_telp')->nullable();
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->string('alamat_lokasi')->nullable();
        $table->json('jawaban');
        $table->string('ip_address', 45)->nullable();
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();

        $table->index(['survey_id', 'submitted_at']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('survey_submissions');
    }
};