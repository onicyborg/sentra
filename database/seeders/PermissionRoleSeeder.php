<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil role & permission dari database
        $roles = DB::table('roles')->pluck('id', 'name')->toArray();
        $permissions = DB::table('permissions')->pluck('id', 'permission_key')->toArray();

        /**
         * MATRIX ROLE → PERMISSION
         * key   = role name
         * value = permission_key yang ALLOWED (true)
         */
        $matrix = [
            // =====================
            // 🔥 ADMIN SYSTEM
            // =====================
            'admin_system' => [
                // Surat Masuk
                'surat_masuk.create',
                'surat_masuk.read',
                'surat_masuk.verify',
                'surat_masuk.distribute',
                'surat_masuk.follow_up',
                'surat_masuk.archive',

                // Surat Keluar
                'surat_keluar.create',
                'surat_keluar.read',
                'surat_keluar.approve',
                'surat_keluar.send',
                'surat_keluar.archive',

                // Arsip
                'archive.read',
                'archive.manage',

                // Notification
                'notification.read',

                // Report
                'report.read',
                'report.export',

                // 🔐 ADMIN SYSTEM
                'user.manage',
                'role.manage',
                'permission.manage',
                'audit.read',
            ],

            // =====================
            // STAF ADMINISTRASI
            // =====================
            'staf_administrasi' => [
                'surat_masuk.create',
                'surat_masuk.read',
                'surat_keluar.create',
                'surat_keluar.read',
            ],

            // =====================
            // KEPALA DINAS
            // =====================
            'kepala_dinas' => [
                'surat_masuk.verify',
                'surat_masuk.distribute',
                'surat_keluar.approve',
                'report.read',
            ],

            // =====================
            // SEKRETARIS DINAS
            // =====================
            'sekretaris_dinas' => [
                'surat_masuk.verify',
                'surat_masuk.distribute',
                'surat_keluar.approve',
                'report.read',
            ],

            // =====================
            // UNIT KERJA
            // =====================
            'unit_kerja' => [
                'surat_masuk.read',
                'surat_masuk.follow_up',
            ],

            // =====================
            // ARSIPARIS
            // =====================
            'arsiparis' => [
                'archive.read',
                'archive.manage',
            ],

            // =====================
            // PENGAWAS
            // =====================
            'pengawas' => [
                'report.read',
                'archive.read',
            ],
        ];

        foreach ($matrix as $roleName => $allowedPermissionKeys) {

            // Safety check: role harus ada
            if (! isset($roles[$roleName])) {
                continue;
            }

            foreach ($permissions as $permissionKey => $permissionId) {
                DB::table('permission_role')->updateOrInsert(
                    [
                        'role_id'       => $roles[$roleName],
                        'permission_id' => $permissionId,
                    ],
                    [
                        'id'      => Str::uuid(),
                        'allowed' => in_array($permissionKey, $allowedPermissionKeys, true),
                    ]
                );
            }
        }
    }
}
