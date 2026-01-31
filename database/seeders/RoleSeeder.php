<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin_system', 'description' => 'Administrator Sistem'],
            ['name' => 'staf_administrasi', 'description' => 'Staf Administrasi'],
            ['name' => 'kepala_dinas', 'description' => 'Kepala Dinas'],
            ['name' => 'sekretaris_dinas', 'description' => 'Sekretaris Dinas'],
            ['name' => 'unit_kerja', 'description' => 'Unit Kerja'],
            ['name' => 'arsiparis', 'description' => 'Arsiparis'],
            ['name' => 'pengawas', 'description' => 'Pengawas / Auditor'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'id' => Str::uuid(),
                'name' => $role['name'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
