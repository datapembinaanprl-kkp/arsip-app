<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan role admin ada
        $role = Role::firstOrCreate(['name' => 'admin']);

        // buat user dan simpan ke variabel
        $user = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@arsip.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // assign role ke user
        $user->assignRole($role);
    }
}