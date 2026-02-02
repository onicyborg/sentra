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
        $rows = [
            ['id' => 'be9749ed-9b19-4841-bae7-2017d9dac8a6', 'name' => 'admin_system',      'description' => 'Administrator Sistem', 'created_at' => '2026-02-01 15:56:53', 'updated_at' => '2026-02-01 15:56:53'],
            ['id' => '83e92c82-7d27-46c2-ab6d-733cc48a9179', 'name' => 'staf_administrasi', 'description' => 'Staf Administrasi',    'created_at' => '2026-02-01 15:56:54', 'updated_at' => '2026-02-01 15:56:54'],
            ['id' => '02b2a961-32cc-4b68-be61-16b54bd7484b', 'name' => 'kepala_dinas',      'description' => 'Kepala Dinas',         'created_at' => '2026-02-01 15:56:54', 'updated_at' => '2026-02-01 15:56:54'],
            ['id' => '5cb155dc-6706-4660-bdf5-bcedd2a7ac13', 'name' => 'sekretaris_dinas',  'description' => 'Sekretaris Dinas',     'created_at' => '2026-02-01 15:56:54', 'updated_at' => '2026-02-01 15:56:54'],
            ['id' => 'a991e2d2-b4fe-4bbf-a38e-b17d60e1c843', 'name' => 'unit_kerja',        'description' => 'Unit Kerja',           'created_at' => '2026-02-01 15:56:54', 'updated_at' => '2026-02-01 15:56:54'],
            ['id' => '31c08ac6-2c90-4cc9-9fb8-744d2cbea49b', 'name' => 'arsiparis',         'description' => 'Arsiparis',             'created_at' => '2026-02-01 15:56:54', 'updated_at' => '2026-02-01 15:56:54'],
            ['id' => 'd69c3c99-8a6f-49c5-b24c-6a87a280e2a1', 'name' => 'pengawas',          'description' => 'Pengawas / Auditor',    'created_at' => '2026-02-01 15:56:54', 'updated_at' => '2026-02-01 15:56:54'],
        ];

        foreach ($rows as $row) {
            DB::table('roles')->updateOrInsert(
                ['id' => $row['id']],
                $row
            );
        }
    }
}
