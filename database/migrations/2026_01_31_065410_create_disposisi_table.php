<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disposisi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('surat_masuk_id');
            $table->uuid('dari_user');
            $table->string('ke_unit');
            $table->text('catatan')->nullable();
            $table->string('status');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('surat_masuk_id')->references('id')->on('surat_masuk')->cascadeOnDelete();
            $table->foreign('dari_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi');
    }
};
