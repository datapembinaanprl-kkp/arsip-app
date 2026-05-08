<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Define all permissions ───────────────────────────
        $permissions = [
            // Dokumen
            'dokumen.viewAny', 'dokumen.view', 'dokumen.create',
            'dokumen.edit',    'dokumen.delete', 'dokumen.approve', 'dokumen.export',

            // User
            'user.viewAny', 'user.create', 'user.edit', 'user.delete',

            // Tim Kerja
            'tim.viewAny', 'tim.create', 'tim.edit', 'tim.delete',

            // Survey
            'survey.viewAny', 'survey.create', 'survey.edit',
            'survey.delete',  'survey.export',

            // Dashboard
            'dashboard.analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─── Define roles with permissions ───────────────────
        $matrix = [
            'admin' => $permissions, // semua permission

            'direktur' => [
                'dokumen.viewAny', 'dokumen.view', 'dokumen.create',
                'dokumen.edit',    'dokumen.delete', 'dokumen.approve', 'dokumen.export',
                'user.viewAny',
                'tim.viewAny',
                'survey.viewAny', 'survey.create', 'survey.edit', 'survey.delete', 'survey.export',
                'dashboard.analytics',
            ],

            'kepala_tim_kerja' => [
                'dokumen.viewAny', 'dokumen.view', 'dokumen.create',
                'dokumen.edit',    'dokumen.delete', 'dokumen.approve',
                'tim.viewAny',
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
        }

        $this->command->info('Roles & permissions seeded successfully.');
    }
}