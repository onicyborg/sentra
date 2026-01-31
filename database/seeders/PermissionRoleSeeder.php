<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'name');
        $permissions = DB::table('permissions')->pluck('id', 'permission_key');

        $matrix = [
            // 🔥 ADMIN SYSTEM
            'admin_system' => array_keys($permissions->toArray()),

            'staf_administrasi' => [
                'surat_masuk.create',
                'surat_masuk.read',
                'surat_keluar.create',
                'surat_keluar.read',
            ],

            'kepala_dinas' => [
                'surat_masuk.verify',
                'surat_masuk.distribute',
                'surat_keluar.approve',
                'report.read',
            ],

            'sekretaris_dinas' => [
                'surat_masuk.verify',
                'surat_masuk.distribute',
                'surat_keluar.approve',
                'report.read',
            ],

            'unit_kerja' => [
                'surat_masuk.read',
                'surat_masuk.follow_up',
            ],

            'arsiparis' => [
                'archive.read',
                'archive.manage',
            ],

            'pengawas' => [
                'report.read',
                'archive.read',
            ],
        ];

        foreach ($matrix as $roleName => $allowedPermissions) {
            foreach ($permissions as $permissionKey => $permissionId) {
                DB::table('permission_role')->insert([
                    'id' => Str::uuid(),
                    'role_id' => $roles[$roleName],
                    'permission_id' => $permissionId,
                    'allowed' => in_array($permissionKey, $allowedPermissions),
                ]);
            }
        }
    }
}
