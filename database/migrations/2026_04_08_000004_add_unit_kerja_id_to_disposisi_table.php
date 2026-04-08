<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disposisi', function (Blueprint $table) {
            $table->uuid('unit_kerja_id')->nullable()->after('ke_unit');
            $table->index('unit_kerja_id');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerja')->nullOnDelete();
        });

        if (Schema::hasColumn('disposisi', 'ke_unit')) {
            $rows = DB::table('disposisi')
                ->select('id', 'ke_unit')
                ->whereNull('unit_kerja_id')
                ->whereNotNull('ke_unit')
                ->where('ke_unit', '<>', '')
                ->get();

            foreach ($rows as $r) {
                $unitId = DB::table('unit_kerja')->where('name', $r->ke_unit)->value('id');
                if (!$unitId) continue;
                DB::table('disposisi')->where('id', $r->id)->update(['unit_kerja_id' => $unitId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('disposisi', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropIndex(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });
    }
};
