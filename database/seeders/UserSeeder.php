<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = Str::uuid();

        DB::table('users')->insert([
            'id' => $adminId,
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Qwerty123*'),
        ]);

        $adminRole = DB::table('roles')->where('name', 'admin_system')->value('id');

        DB::table('role_user')->insert([
            'id' => Str::uuid(),
            'user_id' => $adminId,
            'role_id' => $adminRole,
        ]);
    }
}
