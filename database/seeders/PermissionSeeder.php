<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
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

            // Laporan
            'report.read',
            'report.export',

            // User & System
            'user.manage',
            'role.manage',
            'permission.manage',
            'audit.read',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'id' => Str::uuid(),
                'permission_key' => $permission,
                'description' => ucfirst(str_replace('.', ' ', $permission)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
