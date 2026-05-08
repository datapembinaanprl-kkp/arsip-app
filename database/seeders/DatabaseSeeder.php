<?php

namespace Database\Seeders;

use App\Models\TimKerja;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            TimKerjaSeeder::class,
        ]);

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@webarsip.test'],
            [
                'name'     => 'Administrator',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        // Direktur user
        $direktur = User::firstOrCreate(
            ['email' => 'direktur@webarsip.test'],
            [
                'name'     => 'Direktur Utama',
                'password' => bcrypt('password'),
            ]
        );
        $direktur->assignRole('direktur');

        // Kepala Tim Kerja
        $tim = TimKerja::where('kode', 'ADM')->first();

        $kepala = User::firstOrCreate(
            ['email' => 'kepala.adm@webarsip.test'],
            [
                'name'        => 'Kepala Administrasi',
                'password'    => bcrypt('password'),
                'tim_kerja_id' => $tim->id,
            ]
        );
        $kepala->assignRole('kepala_tim_kerja');

        // Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@webarsip.test'],
            [
                'name'        => 'Staff Administrasi',
                'password'    => bcrypt('password'),
                'tim_kerja_id' => $tim->id,
            ]
        );
        $staff->assignRole('staff');
    }
}