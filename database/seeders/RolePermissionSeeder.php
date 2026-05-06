<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie sebelum seeder
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── 1. Definisi semua permissions ────────────────────
        $permissions = [
            // Dokumen
            'dokumen.lihat',        // Melihat daftar & detail dokumen
            'dokumen.upload',       // Mengunggah dokumen baru
            'dokumen.edit',         // Mengedit metadata dokumen sendiri
            'dokumen.hapus',        // Soft delete dokumen (admin only)
            'dokumen.download',     // Mengunduh file dokumen
            'dokumen.tolak',        // Menolak/reject dokumen ke staf

            // Laporan
            'laporan.lihat',        // Melihat halaman laporan & progres

            // Admin
            'admin.pengguna',       // Kelola pengguna (CRUD)
            'admin.peran',          // Kelola roles & permissions
            'admin.kategori',       // Kelola kategori dokumen
            'admin.sistem',         // Konfigurasi sistem (pengaturan)
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── 2. Definisi roles & assign permissions ───────────

        // ADMIN — akses penuh
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions); // semua permissions

        // STAF — upload, lihat, download dokumen sendiri
        $staf = Role::firstOrCreate(['name' => 'staf', 'guard_name' => 'web']);
        $staf->syncPermissions([
            'dokumen.lihat',
            'dokumen.upload',
            'dokumen.edit',
            'dokumen.download',
        ]);

        // SUPERVISOR — semua staf + bisa reject/tolak
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'dokumen.lihat',
            'dokumen.upload',
            'dokumen.edit',
            'dokumen.download',
            'dokumen.tolak',
            'laporan.lihat',
        ]);

        // DIREKTUR — semua supervisor + laporan tingkat tinggi + hapus dokumen
        $direktur = Role::firstOrCreate(['name' => 'direktur', 'guard_name' => 'web']);
        $direktur->syncPermissions([
            'dokumen.lihat',
            'dokumen.upload',
            'dokumen.edit',
            'dokumen.download',
            'dokumen.tolak',
            'dokumen.hapus',
            'laporan.lihat',
        ]);

        // ─── 3. Buat user default untuk setiap role ───────────
        $this->createDefaultUsers();

        $this->command->info('✅ Roles & Permissions berhasil dibuat.');
        $this->command->table(
            ['Role', 'Permissions'],
            [
                ['admin',      'Semua permissions'],
                ['staf',       'lihat, upload, edit, download'],
                ['supervisor', 'lihat, upload, edit, download, tolak, laporan'],
                ['direktur',   'lihat, upload, edit, download, tolak, hapus, laporan'],
            ]
        );
    }

    private function createDefaultUsers(): void
    {
        $defaults = [
            ['name' => 'Admin Sistem',   'email' => 'admin@arsip.id',      'role' => 'admin'],
            ['name' => 'Staf Arsip',     'email' => 'staf@arsip.id',       'role' => 'staf'],
            ['name' => 'Supervisor',     'email' => 'supervisor@arsip.id', 'role' => 'supervisor'],
            ['name' => 'Direktur',       'email' => 'direktur@arsip.id',   'role' => 'direktur'],
        ];

        foreach ($defaults as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
            // Assign role (hapus role lama dulu agar tidak duplikat)
            $user->syncRoles([$data['role']]);
        }

        $this->command->info('👤 User default dibuat (password: password123)');
    }
}