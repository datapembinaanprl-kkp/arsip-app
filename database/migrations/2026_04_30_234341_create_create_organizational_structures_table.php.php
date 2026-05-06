<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');                          // Jabatan/role title
            $table->string('photo')->nullable();                 // Stored path relative to storage/app/public
            $table->unsignedBigInteger('parent_id')->nullable(); // null = root/top-level member
            $table->unsignedInteger('order')->default(0);        // Sort order within same parent level
            $table->timestamps();

            // Self-referencing FK for hierarchy
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('organizational_structures')
                  ->onDelete('set null'); // Promote children to root if parent is deleted

            $table->index(['parent_id', 'order']); // Optimize hierarchy queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_structures');
    }
};