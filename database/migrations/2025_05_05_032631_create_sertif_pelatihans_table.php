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
        Schema::create('sertif_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesi_id')->constrained();
            $table->string('path_sertif_pelatihan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertif_pelatihans');
    }
};
