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
        $rows = [
            // Surat Masuk
            ['id'=>'28230bdf-0d49-4d14-9ed7-8cf5d9e4fd75','permission_key'=>'surat_masuk.create','description'=>'Surat_masuk create','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'d81fa8c4-2f9e-4334-993f-60e743d28f55','permission_key'=>'surat_masuk.read','description'=>'Surat_masuk read','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'c81c3e56-c92f-4e35-afda-75078468cf02','permission_key'=>'surat_masuk.verify','description'=>'Surat_masuk verify','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'ee6bf1fd-28ba-43ce-bb4e-dbe6c49e19f8','permission_key'=>'surat_masuk.distribute','description'=>'Surat_masuk distribute','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'2a5d4726-00e4-4efb-853b-4f44a477281b','permission_key'=>'surat_masuk.follow_up','description'=>'Surat_masuk follow_up','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'a3664a6a-102f-4842-9b22-182c42192684','permission_key'=>'surat_masuk.archive','description'=>'Surat_masuk archive','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],

            // Surat Keluar
            ['id'=>'6d3e625d-c97a-4192-ba66-4cadb840bb67','permission_key'=>'surat_keluar.create','description'=>'Surat_keluar create','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'f2c1459b-b668-451d-a7ce-d5d07f5c065f','permission_key'=>'surat_keluar.read','description'=>'Surat_keluar read','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'85cf510a-4cfa-48a7-89f5-ef7ae3609f9b','permission_key'=>'surat_keluar.approve','description'=>'Surat_keluar approve','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'ce2fdb07-ef26-4f8d-85b3-efa3330a00a0','permission_key'=>'surat_keluar.send','description'=>'Surat_keluar send','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'42b87b31-b707-441f-a7f9-e72ad24d06c8','permission_key'=>'surat_keluar.archive','description'=>'Surat_keluar archive','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],

            // Arsip
            ['id'=>'5d73dc4e-9c50-4ae4-9630-46b67dbaed51','permission_key'=>'archive.read','description'=>'Archive read','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'5f6d6809-416f-4788-bb1c-8bb5618b55a1','permission_key'=>'archive.manage','description'=>'Archive manage','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],

            // Laporan
            ['id'=>'348c14e6-52c7-4c81-9c22-871827f400fa','permission_key'=>'report.read','description'=>'Report read','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'589620bd-b822-4ad9-80c3-a8cddca524a5','permission_key'=>'report.export','description'=>'Report export','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],

            // User & System
            ['id'=>'f603bfc9-1111-48ac-869b-52e549b3cc00','permission_key'=>'user.manage','description'=>'User manage','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'e2ecb66b-7804-4063-90c1-62a2adb8fc12','permission_key'=>'role.manage','description'=>'Role manage','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'a75cb502-68f4-4934-8d60-43688faefcec','permission_key'=>'permission.manage','description'=>'Permission manage','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
            ['id'=>'30dad593-d206-4b2a-99da-d7e4c022899b','permission_key'=>'audit.read','description'=>'Audit read','created_at'=>'2026-02-01 15:56:54','updated_at'=>'2026-02-01 15:56:54'],
        ];

        foreach ($rows as $row) {
            DB::table('permissions')->updateOrInsert(
                ['id' => $row['id']],
                $row
            );
        }
    }
}
