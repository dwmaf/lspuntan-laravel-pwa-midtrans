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
            $table->foreignId('sertification_id')->constrained();
            $table->text('rincian_pengumuman_asesmen');
            $table->foreignId('rincian_pengumuman_asesmen_dibuat_oleh')->nullable()->constrained('users');
            $table->timestamp('rincian_pengumuman_asesmen_dibuat_pada')->nullable();
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
