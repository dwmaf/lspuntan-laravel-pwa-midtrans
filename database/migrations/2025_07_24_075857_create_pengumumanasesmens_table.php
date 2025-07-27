<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // tabel untuk nyimpan daftar pengumuman asesmen, bisa aja jika ada lebih dari 1 pengumuman yg akan dibuat
    public function up(): void
    {
        Schema::create('pengumumanasesmens', function (Blueprint $table) {
            $table->id();
            $table->text('rincian_pengumuman_asesmen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumanasesmens');
    }
};
