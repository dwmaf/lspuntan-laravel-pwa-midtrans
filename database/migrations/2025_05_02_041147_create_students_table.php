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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('nik')->nullable();
            $table->string('tmpt_tgl_lhr')->nullable();
            $table->string('kelamin')->nullable();
            $table->string('kebangsaan')->nullable();
            $table->string('no_tlp_rmh')->nullable();
            $table->string('no_tlp_kntr')->nullable();
            $table->string('no_tlp_hp')->nullable();
            $table->string('kualifikasi_pendidikan')->nullable();
            $table->string('nama_institusi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('alamat_kantor')->nullable();
            $table->string('no_tlp_email_fax')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_ktm')->nullable();
            $table->string('foto_khs')->nullable();
            $table->string('pas_foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
