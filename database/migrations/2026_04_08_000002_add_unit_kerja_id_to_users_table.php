<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('unit_kerja_id')->nullable()->after('password');
            $table->index('unit_kerja_id');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropIndex(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });
    }
};
