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
        Schema::create('skemas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_skema');
            $table->string('format_apl_1')->nullable();
            $table->string('format_apl_2')->nullable();
            $table->string('format_ak_1')->nullable();
            $table->string('format_ak_2')->nullable();
            $table->string('format_ak_3')->nullable();
            $table->string('format_ak_4')->nullable();
            $table->string('format_ac_1')->nullable();
            $table->string('format_map_1')->nullable();
            $table->string('format_ia_1')->nullable();
            $table->string('format_ia_2')->nullable();
            $table->string('format_ia_5')->nullable();
            $table->string('format_ia_6')->nullable();
            $table->string('format_ia_7')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skemas');
    }
};
