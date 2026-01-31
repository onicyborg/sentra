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
        Schema::create('lampiran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('surat_masuk_id')->nullable();
            $table->uuid('surat_keluar_id')->nullable();
            $table->string('file_path');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('surat_masuk_id')->references('id')->on('surat_masuk')->cascadeOnDelete();
            $table->foreign('surat_keluar_id')->references('id')->on('surat_keluar')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran');
    }
};
