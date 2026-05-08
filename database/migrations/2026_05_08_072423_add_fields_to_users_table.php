<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tim_kerja_id')
                  ->nullable()
                  ->after('email')
                  ->constrained('tim_kerjas')
                  ->nullOnDelete();

            $table->string('phone', 20)->nullable()->after('tim_kerja_id');
            $table->string('avatar')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tim_kerja_id');
            $table->dropColumn(['phone', 'avatar', 'last_login_at']);
        });
    }
};