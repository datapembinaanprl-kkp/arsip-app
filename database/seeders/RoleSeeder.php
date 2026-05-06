<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    use Spatie\Permission\Models\Role;

    public function run(): void
    {
    $roles = [
        'admin',
        'direktur',
        'supervisor',
        'staf'
    ];

    foreach ($roles as $role) {
        Role::firstOrCreate([
            'name' => $role,
            'guard_name' => 'web'
        ]);
    }
    }
}
