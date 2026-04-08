<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            'Unit Kerja Default',
        ];

        foreach ($units as $name) {
            $existing = DB::table('unit_kerja')->where('name', $name)->first();

            if ($existing) {
                DB::table('unit_kerja')->where('id', $existing->id)->update([
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('unit_kerja')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
