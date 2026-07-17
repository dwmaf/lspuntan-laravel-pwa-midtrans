<?php

use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusFinalAsesi;
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
        Schema::create('asesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa');
            $table->foreignId('sertifikasi_id')->constrained('sertifikasi');
            $table->foreignId('asesor_id')->nullable()->constrained('asesor')->nullOnDelete();
            $table->string('status_berkas')->default(StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN->value);
            $table->string('status_final')->default(StatusFinalAsesi::BELUM_DITETAPKAN->value);
            $table->string('tujuan_sert');
            $table->string('apl_1');
            $table->string('apl_2');
            $table->string('transkrip_nilai');
            $table->string('foto_ktm');
            $table->string('bukti_bayar');
            $table->text('catatan_perbaikan')->nullable();
            $table->text('rekap_nilai');
            $table->string('path_file_asesmen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesi');
    }
};
