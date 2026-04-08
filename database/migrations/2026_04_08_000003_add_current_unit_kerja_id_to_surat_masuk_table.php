<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->uuid('current_unit_kerja_id')->nullable()->after('created_by');
            $table->index('current_unit_kerja_id');
            $table->foreign('current_unit_kerja_id')->references('id')->on('unit_kerja')->nullOnDelete();
        });

        if (Schema::hasTable('disposisi') && Schema::hasColumn('disposisi', 'ke_unit')) {
            $latest = DB::table('disposisi as d')
                ->join(DB::raw('(SELECT surat_masuk_id, MAX(created_at) AS max_created_at FROM disposisi GROUP BY surat_masuk_id) AS x'), function ($join) {
                    $join->on('d.surat_masuk_id', '=', 'x.surat_masuk_id');
                    $join->on('d.created_at', '=', 'x.max_created_at');
                })
                ->select('d.surat_masuk_id', 'd.ke_unit')
                ->get();

            foreach ($latest as $row) {
                if (!$row->ke_unit) continue;
                $unitId = DB::table('unit_kerja')->where('name', $row->ke_unit)->value('id');
                if (!$unitId) continue;
                DB::table('surat_masuk')->where('id', $row->surat_masuk_id)->update([
                    'current_unit_kerja_id' => $unitId,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropForeign(['current_unit_kerja_id']);
            $table->dropIndex(['current_unit_kerja_id']);
            $table->dropColumn('current_unit_kerja_id');
        });
    }
};
