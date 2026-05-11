<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role - enum string
            $table->enum('role', ['admin', 'user'])->default('user');

            // Status - enum string
            $table->enum('status', ['active', 'inactive'])->default('active');

            //Data Personal 
            $table->string('nip', 30)->nullable()->after('avatar');
            $table->string('pangkat_golongan', 100)->nullable()->after('nip');
            $table->string('jabatan_fungsional', 100)->nullable()->after('pangkat_golongan'); //jabatan fungsional seperti peneliti, analis, dsb    
            $table->string('SPT', 100)->nullable()->after('jabatan_fungsional'); //unit kerja seperti balitbangda, dinas, dsb
            $table->string('SKP', 100)->nullable()->after('SPT'); // sasaran kinerja pegawai


            // Last login timestamp            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_login')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'status', 'avatar', 'nip', 'pangkat_golongan', 
                'jabatan_fungsional', 'SPT', 'SKP', 'last_login'
            ]);
        });
    }
};
