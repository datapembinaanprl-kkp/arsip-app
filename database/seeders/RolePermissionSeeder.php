<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Define all permissions ───────────────────────────────────────────
        $permissions = [
            // Dokumen
            'dokumen.viewAny', 'dokumen.view', 'dokumen.create',
            'dokumen.edit',    'dokumen.delete', 'dokumen.approve', 'dokumen.export',
            // User
            'users.viewAny', 'users.create', 'users.edit', 'users.delete',
            // Tim Kerja
            'tim-kerja.viewAny', 'tim-kerja.create', 'tim-kerja.edit', 'tim-kerja.delete',
            // Survey
            'survey.viewAny', 'survey.create', 'survey.edit',
            'survey.delete',  'survey.export',
            // Activity Log
            'activity-logs.viewAny',
            // Dashboard
            'dashboard.analytics',
            // Setting
            'setting.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─── Permission matrix per role ───────────────────────────────────────
        // Admin dan Direktur memiliki permission yang sama (semua)
        $sharedTopPermissions = $permissions;

        $matrix = [
            'admin'           => $sharedTopPermissions,
            'direktur'        => $sharedTopPermissions,
            'kepala_tim_kerja' => [
                'dokumen.viewAny', 'dokumen.view', 'dokumen.create',
                'dokumen.edit',    'dokumen.delete', 'dokumen.approve',
                'tim-kerja.viewAny',
                'survey.viewAny',
                'dashboard.analytics',
            ],
            'staff' => [
                'dokumen.viewAny', 'dokumen.view',
                'dokumen.create',  'dokumen.edit',
            ],
        ];

        foreach ($matrix as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
            $this->command->info("Role '{$roleName}' synced with " . count($rolePermissions) . " permissions.");
        }

        // ─── Assign admin role ke user pertama ────────────────────────────────
        $adminUser = User::first(); 
        if ($adminUser) {
            $adminUser->syncRoles(['admin']);
            $this->command->info("Assigned 'admin' role to: {$adminUser->email}");
        } else {
            $this->command->warn('No users found.');
        }

        $this->command->info('Done.');
    }
}