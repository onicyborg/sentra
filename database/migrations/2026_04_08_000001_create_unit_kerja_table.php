<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        if (Schema::hasTable('disposisi') && Schema::hasColumn('disposisi', 'ke_unit')) {
            $units = DB::table('disposisi')
                ->select('ke_unit')
                ->whereNotNull('ke_unit')
                ->where('ke_unit', '<>', '')
                ->distinct()
                ->orderBy('ke_unit')
                ->pluck('ke_unit');

            foreach ($units as $name) {
                $exists = DB::table('unit_kerja')->where('name', $name)->exists();
                if ($exists) {
                    continue;
                }

                DB::table('unit_kerja')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_kerja');
    }
};
