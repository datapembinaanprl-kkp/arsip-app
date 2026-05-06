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
    // Skip jika tabel sudah ada (avoid duplicate table error)
    if (Schema::hasTable('survey_questions')) {
        return;
    }

    Schema::create('survey_questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('survey_id')
              ->constrained('surveys')
              ->onDelete('cascade');
        $table->string('label');
        $table->enum('type', [
            'text', 'textarea', 'radio', 'checkbox', 'select', 'date', 'rating',
        ]);
        $table->json('options')->nullable();
        $table->boolean('required')->default(false);
        $table->unsignedSmallInteger('order')->default(0);
        $table->timestamps();

        $table->index(['survey_id', 'order']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
