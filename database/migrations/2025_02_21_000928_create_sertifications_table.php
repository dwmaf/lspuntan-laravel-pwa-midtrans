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
        Schema::create('sertifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_id')->constrained();
            $table->foreignId('asesor_id')->constrained();
            $table->text('rincian_tugas_asesmen')->nullable();
            $table->text('rincian_praasesmen')->nullable();
            $table->date('tgl_apply_dibuka');
            $table->date('tgl_apply_ditutup');
            $table->dateTime('tgl_bayar_ditutup');
            $table->integer('harga')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifications');
    }
};
