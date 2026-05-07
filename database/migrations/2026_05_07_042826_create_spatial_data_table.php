<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spatial_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('kategori', 1000)->index();
            $table->text('deskripsi')->nullable();
            $table->jsonb('properties')->nullable()->default('{}');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE spatial_data ADD COLUMN geometry geometry(Geometry, 4326)');
        DB::statement('CREATE INDEX idx_spatial_data_geometry ON spatial_data USING GIST (geometry)');
    
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spatial_data');
    }
};
