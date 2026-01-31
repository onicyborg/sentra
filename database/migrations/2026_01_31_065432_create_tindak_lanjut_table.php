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
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('surat_masuk_id');
            $table->string('unit');
            $table->text('deskripsi');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('surat_masuk_id')->references('id')->on('surat_masuk')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
