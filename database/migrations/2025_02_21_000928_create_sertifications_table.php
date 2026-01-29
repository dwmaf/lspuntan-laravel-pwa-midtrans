<?php

use App\Enums\StatusSertifikasi;
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
            $table->string('tuk')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('bank')->nullable();
            $table->string('atas_nama_rek')->nullable();
            $table->integer('biaya')->nullable();
            $table->date('tgl_apply_dibuka');
            $table->dateTime('tgl_apply_ditutup');
            $table->date('tgl_asesmen_mulai')->nullable();
            $table->date('tgl_asesmen_selesai')->nullable();
            $table->string('status')->default(StatusSertifikasi::BERLANGSUNG->value);
            $table->softDeletes();
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
