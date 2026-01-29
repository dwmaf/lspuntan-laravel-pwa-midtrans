<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // di sini tabel untuk nyimpan file" yg diattach mahasiswa ketika ngumpulin tugas asesmen
    public function up(): void
    {
        Schema::create('asesiasesmens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesi_id')->constrained();
            $table->string('ak_1')->nullable();
            $table->string('ak_2')->nullable();
            $table->string('ak_3')->nullable();
            $table->string('ak_4')->nullable();
            $table->string('ac_1')->nullable();
            $table->string('map_1')->nullable();
            $table->string('ia_1')->nullable();
            $table->string('ia_2')->nullable();
            $table->string('ia_5')->nullable();
            $table->string('ia_6')->nullable();
            $table->string('ia_7')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesiasesmens');
    }
};
