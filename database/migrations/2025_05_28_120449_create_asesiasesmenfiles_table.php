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
        Schema::create('asesiasesmenfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesi_id')->constrained();
            $table->string('asesi_asesmen_attachment_file')->nullable();
            $table->string('file_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesiasesmenfiles');
    }
};
