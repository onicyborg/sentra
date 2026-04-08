<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ukPaudPnfId = DB::table('unit_kerja')->where('name', 'Bidang Pembinaan PAUD & PNF (Pendidikan Nonformal)')->value('id');
        $ukSdId = DB::table('unit_kerja')->where('name', 'Bidang Pembinaan Sekolah Dasar (SD)')->value('id');
        $ukSmpId = DB::table('unit_kerja')->where('name', 'Bidang Pembinaan SMP')->value('id');
        $ukPtkId = DB::table('unit_kerja')->where('name', 'Bidang Ketenagaan (PTK)')->value('id');

        // Seed users per current data snapshot
        $users = [
            [
                'id' => 'dd9e2773-5336-44e4-925b-ba8333f1b698',
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => '$2y$12$8LeixxvQRVFrqJPU8NUASObflqkLalM1Gl1lF6ilWJq5/.CnIfTLK',
                'unit_kerja_id' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 'e0b1177f-b0d5-4589-8cdb-619916a709af',
                'name' => 'Tony Stark',
                'email' => 'tony@example.com',
                'password' => '$2y$12$VbQHt3ud.Gy4oTmOXyVwMOp/LXfUjPwpZR6ouwCip/4PrzaIQaBum',
                'unit_kerja_id' => null,
                'created_at' => '2026-02-01 15:57:54',
                'updated_at' => '2026-02-01 15:57:54',
            ],
            [
                'id' => 'af4d510c-0837-4adc-b559-26a97215d6d1',
                'name' => 'Bruce Banner',
                'email' => 'bruce@example.com',
                'password' => '$2y$12$I2c1wqAeSb65.vTMATFCvOqCvfmdWulSFN8wCxz4bs4sBq42gkgSW',
                'unit_kerja_id' => null,
                'created_at' => '2026-02-01 15:59:31',
                'updated_at' => '2026-02-01 15:59:31',
            ],
            [
                'id' => '77bdf118-c84e-438d-b444-98bd4243f1db',
                'name' => 'Thor Odinson',
                'email' => 'thor@example.com',
                'password' => '$2y$12$i2wck0Di7e9mAnVVNx.tHePLOOX8AxxI5qIt9PiLqSXL2IpvOjyK.',
                'unit_kerja_id' => $ukSmpId,
                'created_at' => '2026-02-01 17:02:28',
                'updated_at' => '2026-02-01 17:02:28',
            ],
            [
                'id' => 'fd0c5c5f-5a63-4a2b-8fdf-2f6e4e51b1c0',
                'name' => 'Steve Rogers',
                'email' => 'steve@example.com',
                'password' => Hash::make('password'),
                'unit_kerja_id' => $ukPaudPnfId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'a9f5d0db-5bdb-4c1d-8fbe-8d6b2a1d9d03',
                'name' => 'Natasha Romanoff',
                'email' => 'natasha@example.com',
                'password' => Hash::make('password'),
                'unit_kerja_id' => $ukSdId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '3f2b12a1-6a1d-4cf4-9e4f-7e4b9e7e2d65',
                'name' => 'Clint Barton',
                'email' => 'clint@example.com',
                'password' => Hash::make('password'),
                'unit_kerja_id' => $ukPtkId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $u) {
            DB::table('users')->updateOrInsert(
                ['id' => $u['id']],
                $u
            );
        }

        // Seed role assignments (role_user)
        $roleUsers = [
            [
                'id' => '5cdc1dc3-55ca-48e3-a143-f9061fe1d29b',
                'user_id' => 'dd9e2773-5336-44e4-925b-ba8333f1b698',
                'role_id' => 'be9749ed-9b19-4841-bae7-2017d9dac8a6', // admin_system
            ],
            [
                'id' => '8bf94cea-6206-4042-91dd-f97bb5360ccf',
                'user_id' => 'e0b1177f-b0d5-4589-8cdb-619916a709af',
                'role_id' => '83e92c82-7d27-46c2-ab6d-733cc48a9179', // staf_administrasi
            ],
            [
                'id' => 'dad0a5cf-5bb3-4b63-8208-5a5737ec3501',
                'user_id' => 'af4d510c-0837-4adc-b559-26a97215d6d1',
                'role_id' => '02b2a961-32cc-4b68-be61-16b54bd7484b', // kepala_dinas
            ],
            [
                'id' => '2271d6e7-d402-45f0-9b5f-9d78c7a2a1ff',
                'user_id' => '77bdf118-c84e-438d-b444-98bd4243f1db',
                'role_id' => 'a991e2d2-b4fe-4bbf-a38e-b17d60e1c843', // unit_kerja
            ],
            [
                'id' => '7a1e6e0b-7c06-4f3e-9ef8-72f2c9b004d1',
                'user_id' => 'fd0c5c5f-5a63-4a2b-8fdf-2f6e4e51b1c0',
                'role_id' => 'a991e2d2-b4fe-4bbf-a38e-b17d60e1c843', // unit_kerja
            ],
            [
                'id' => '5e7c2f1c-2b93-4c7a-8e6f-b8f8e0d10e31',
                'user_id' => 'a9f5d0db-5bdb-4c1d-8fbe-8d6b2a1d9d03',
                'role_id' => 'a991e2d2-b4fe-4bbf-a38e-b17d60e1c843', // unit_kerja
            ],
            [
                'id' => 'e22a975a-8e54-4fb1-b3d5-4b7a2d45711f',
                'user_id' => '3f2b12a1-6a1d-4cf4-9e4f-7e4b9e7e2d65',
                'role_id' => 'a991e2d2-b4fe-4bbf-a38e-b17d60e1c843', // unit_kerja
            ],
        ];

        foreach ($roleUsers as $ru) {
            DB::table('role_user')->updateOrInsert(
                ['id' => $ru['id']],
                $ru
            );
        }
    }
}
