<?php

use App\Enums\StatusAksesMenuAsesmen;
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
        Schema::create('asesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('sertification_id')->constrained();
            $table->string('status_berkas')->default(StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN->value);
            $table->string('status_akses_asesmen')->default(StatusAksesMenuAsesmen::BELUM_DIBERIKAN->value);
            $table->string('status_final')->default(StatusFinalAsesi::BELUM_DITETAPKAN->value);
            $table->string('tujuan_sert');
            $table->string('apl_1');
            $table->string('apl_2');
            $table->string('foto_ktm')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan_perbaikan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesis');
    }
};
